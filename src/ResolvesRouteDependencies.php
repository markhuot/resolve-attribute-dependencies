<?php

namespace markhuot\attrdeps;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Reflector;
use markhuot\attrdeps\Casts\Cast;
use markhuot\attrdeps\Resolvers\FromAuth;
use markhuot\attrdeps\Resolvers\FromRequest;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait ResolvesRouteDependencies
{
    public function resolveMethodDependencies(array $parameters, ReflectionFunctionAbstract $reflector)
    {
        $parameters = parent::resolveMethodDependencies($parameters, $reflector);
        $rules = $this->resolveMethodValidationRules($reflector);

        throw_if(count($parameters) !== count($rules), new \ErrorException(
            message: 'Parameter mismatch. Did you forget to add a #[FromRequest] attribute to a parameter?',
            filename: $reflector->getFileName(),
            line: $reflector->getStartLine(),
        ));

        $this->validateMethodDependencies($parameters, $rules);

        return $parameters;
    }

    protected function transformDependency(ReflectionParameter $parameter, $parameters, $skippableValue)
    {
        if ($parameter->getAttributes(FromAuth::class)) {
            $dependency = auth()->user();
        }

        else if ($attributes = $parameter->getAttributes(FromRequest::class)) {
            $key = $attributes[0]->getArguments()['key'] ?? $parameter->getName();
            $dependency = request()->input($key);
        }

        else {
            $dependency = parent::transformDependency($parameter, $parameters, $skippableValue);
        }


        $dependency = collect($parameter->getAttributes())
            ->map(fn (\ReflectionAttribute $attribute) => $attribute->newInstance())
            ->filter(fn ($attribute) => $attribute instanceof Cast)
            ->whenEmpty(function ($collection) use ($parameter) {
                $desiredType = Reflector::getParameterClassName($parameter);
                if ($desiredType) {
                    $caster = app(CastManager::class)->getCastFor($desiredType);
                    if ($caster) {
                        $collection->push(new $caster);
                    }
                }
                return $collection;
            })
            ->reduce(fn ($carry, $caster) => $caster($carry), $dependency);

        return $dependency;
    }

    protected function resolveMethodValidationRules(ReflectionFunctionAbstract $reflector)
    {
        return collect($reflector->getParameters())
            ->mapWithKeys(fn (ReflectionParameter $parameter, $index) => [
                $parameter->getName() => $this->getValidationRulesForParameter($parameter)
            ])
            ->toArray();
    }

    protected function validateMethodDependencies($parameters, $rules)
    {
        $keyedParameters = collect($rules)->keys()
            ->combine(array_values($parameters))
            ->all();

        $validator = Validator::make($keyedParameters, $rules);
        if (request()->isMethodSafe()) {
            throw_if($validator->fails(), new HttpException(422, $validator->messages()->first()));
        }
        else {
            $validator->validate();
        }
    }

    protected function getValidationRulesForParameter(ReflectionParameter $parameter)
    {
        $rules = [];

        if (! $parameter->allowsNull()) {
            $rules[] = 'required';
        }

        foreach ($parameter->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();

            if ($instance instanceof \markhuot\attrdeps\Validation\Validator) {
                $rules[] = $instance->toRules();
            }
        }

        return $rules;
    }
}
