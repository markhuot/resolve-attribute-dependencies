# Resolve Attribute Dependencies

Install via `composer`,

```bash
composer require markhuot/resolve-attribute-dependencies
```

Use PHP8 attributes to resolve dependencies from the request, auth, or other sources directly in to your controller.
All attributes are automatically validated via the Laravel validator and are fully type-safe once resolved. For example,
an attribute of `#[FromRequest, Email] string $email` will be validated as required (because `string` is not nullable)
and as a valid email address. Inside your controller you can use `$email` with confidence. If a parameter does not pass
validation a traditional `ValidationException` will be thrown. On `GET` requests this will return a 422 error. On `POST`
requests this will `redirect()->back()` with the proper `$errors` populated.

```php
use markhuot\attrdeps\Resolvers\FromRequest;
use markhuot\attrdeps\Resolvers\FromAuth;
use markhuot\attrdeps\Validation\Unique;

Route::get('/posts', function (
    #[FromRequest] ?string $query # pulled out of the request, for a GET, the query string
) {
    return view('posts.index', [
        'query' => $query,
    ]);
});

Route::post('/posts', function (
    #[FromAuth] User $user, # pulled from auth()->user() and because it's not nullable, forces a user to be logged in
    #[FromRequest] string $title, # pulled from the request body and validated as required
    #[FromRequest, Unique('posts','slug')] string $slug, # validated as unique within the posts table by slug
    #[FromRequest] ?string $body, # an optional field
) {
    $post = new Post;
    $post->author_id = $user->id;
    $post->title = $title;
    $post->slug = $slug;
    $post->body = $body;
    $post->save();
    
    return redirect()->back();
});
```
