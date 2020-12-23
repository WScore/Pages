WScore.Pages
============

A page controller for good and old (and notorious) PHP code.

This is a component created with the hope that it will make things a little easier for people (i.e. me) who have to maintain PHP code written in the old style.

For example, the following

`http://example.com/example.php`

The goal is to give you the freedom to develop with controllers, even if you access PHP directly.

### License

MIT License

### Installation

PHP 5.6 is targeted.
only available in ~~beta~~ alpha version.

```sh
composer require "wscore/pages: ^0.1"
```

### Demo

```sh
git clone https://github.com/WScore/Pages
cd Pages
composer install
cd public
php -S localhost:8000 index.php
```

Access `localhost:8000` in your browser.

Simple Usage
----------

It's relatively normal. 

1. create a `Controller` for the page and generate an object. 
2. Execute the controller with `Dispatch`. 
3. Create an HTML page under the directory for the view.

### Create a Controller.

Create it by inheriting from `ControllerAbstract`.

```php
use WScore\Pages\AbstractController;

class MyController extends AbstractController {
    private $user;
    public function __construct($loginUser) {
        $this->user = $loginUser;
    }
    public function onGet($id) {
        return $this->render('user.php', [
            'user' => $this->user,
        ]);
    }
}
```

### Run the controller.

Execute (`Dispatch`) the Controller in a PHP file.


```php
use Laminas\Diactoros\ServerRequestFactory;
use WScore\Pages\Dispatch;

$request = ServerRequestFactory::fromGlobals();
$controller = new DemoController();

$view = Dispatch::create($controller, __DIR__ . '/views')
    ->handle($request);
$view->render();
```

`Dispatch` is used for session management and CSRF token checking.

The method name of the controller to be executed is determined from one of the following.

- From the HTTP method name.
- From the value of "act" of GET/POST, `onMethod` is called.

If you access the site normally, `onGet` will be called.
If you post with a form, it is `onPost`.

### View file

In the sample code, "`__DIR__ . '/views'`" in the sample code is the directory for views.

Create "`user.php`" in this directory.

```php
use WScore\Pages\View\Data;
/** @var Data $_view */

$user = $_view->get('user');
?>
<! -- show user -->
```

- `$this` is the object to return from the controller Dispatch (`PaveView`).
- `$view` is an object that holds parameters for drawing (`Data`).

Other Functions
----------

### CSRF tokens.

Outputs a CSRF token from the `$view` object.

```php
use WScore\Pages\View\Data;
/** @var Data $_view */

echo $_view->makeCsRfToken();
```

Whenever posted, check for a CSRF token.
If the check fails, it will result in a Critical error.

### Critical error.

This is a special error for `PageView`.

```php
use WScore\Pages\PageView;
/** @var PageView $view */

if ($view->isCritical()) {
    $view->setRender('critical.php');
}
$view->render();
```

- [ ] If you catch an exception when executing the controller, it will be a Critical error.

### Messages and Flushing

You can specify a message in the controller's `message` and `error`.

```php
use WScore\Pages\AbstractController;

class MsgController extends AbstractController
{
    public function onGet()
    {
        $this->message('please try this demo!');
        $this->error('maybe not!');
        return $this->render('message.php');
    }
}
```

In view PHP, you can display messages, as;

```php
use WScore\Pages\PageView;
/** @var PageView $view */

echo $view->alert();
```

### Flash message

```php
use WScore\Pages\AbstractController;

class MsgController extends AbstractController
{
    public function onGet()
    {
        $this->flashMessage('thank you!');
        $this->flashError('sorry!');
        return $this->location('example.php');
    }
}
````

With the same PHP code as the message, you can display.


### XSS protection

t.b.w.

### HTML Forms

t.b.w

# Credits

README.md translated with www.DeepL.com/Translator (free version).

then some fixed by me.