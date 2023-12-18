<?php

namespace markhuot\attrdeps;

use Illuminate\Support\Facades\Validator;
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

        $rules = collect($reflector->getParameters())
            ->mapWithKeys(fn (ReflectionParameter $parameter, $index) => [
                $parameter->getName() => $this->getValidationRules($parameter)
            ])
            ->toArray();

        throw_if(count($parameters) !== count($rules), new \ErrorException(
            message: 'Parameter mismatch. Did you forget to add a #[FromRequest] attribute to a parameter?',
            filename: $reflector->getFileName(),
            line: $reflector->getStartLine(),
        ));

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

        return $parameters;
    }

    protected function transformDependency(ReflectionParameter $parameter, $parameters, $skippableValue)
    {
        if ($parameter->getAttributes(FromAuth::class)) {
            return auth()->user();
        }

        if ($attributes = $parameter->getAttributes(FromRequest::class)) {
            $key = $attributes[0]->getArguments()['key'] ?? $parameter->getName();
            return request()->input($key);
        }

        return parent::transformDependency($parameter, $parameters, $skippableValue);
    }

    protected function getValidationRules(ReflectionParameter $parameter)
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
