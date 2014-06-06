WScore.Pages
============

A simple page controller for legacy php code.

This package provides a plain, simple, and easy to use
page controller (i.e. dispatcher) for good old legacy
php code, with a high hope to reduce the pain of
maintaining the old old php code.

### License

MIT License

### Installation

As usual, use composer to install this repository for your project.
Not version is set currently.

```json
"require": {
    "wscore/pages":"dev-master"
}
```

Quick Overview
--------------

1.   Write your controller by extending ControllerAbstract.
2.   Dispatch the controller with Dispatch.
3.   Get view object to construct your HTML.

### Sample Code

```php
// 1. your controller.
class MyController extends ControllerAbstract {
    protected $dao;
    public_function __construct( $dao ) {
        $this->dao = $dao;
    }
    public function onGet($id) {
        $data = $this->dao->find($id);
        $this->set( $data );
    }
}
// 2. get dispatcher with factory.
$page = Factory::getDispatch(
    new MyController( new Dao() )
);
// 3. execute controller and get view object.
$view = $page->execute();
$data = $view->get('data');
```


Controller
----------

write about how to code Controller.


View
----

write about View object. 

Dispatcher
----------

write a bit about the dispatcher. 

