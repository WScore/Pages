WScore.Pages
============

A simple page controller package for legacy php code.

This package provides a plain, simple, and easy to use
page controller (i.e. dispatcher) for good old legacy
php code, with a high hope to ease maintaining the
old and painful php code. Yap, this is for me.

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

Quite ordinary setups.

1.   Prepare ```Controller``` class, and construct it.
2.   Execute methods using ```Dispatch``.
3.   The result is ```View``` object used for HTML.


Sample Code
-----------

### a simple Controller class

extend ```ControllerAbstract``` class.

```php
class MyController extends ControllerAbstract {
    protected $dao;
    public_function __construct( $dao ) {
        $this->dao = $dao;
    }
    public function onIndex() {
        return $this->dao->getAll();
    }
    public function onGet($id) {
        $data = $this->dao->find($id)
        $this->pass( 'id', $id );
        $this->set(  'data', $data );
        return [ 'title' => 'Got A Data!' ];
    }
}
```

The ```onHttpMethodName``` methods are executed based on
http method. For instance, ```onGet``` method is executed
if the http method is 'get'.
 to overwrite the http method, use ```_method``` value.

The arguments in the on-methods are populated with values
 taken from $_REQUEST. null is set if value is not found.

To set value in the view, use ```set``` method. Or, return
 an array from the on-method.

Also, using ```pass``` method, the value will be passed
to the next request via hidden tags.

### Dispatch

Use factory class.

```php
$app = \WScore\Pages\Factory::getDispatch(
    new MyController( new MyDao() )
);
$view = $app->execute();
```

You can specify the method to execute.

```php
$view = $app->execute( 'index' ); // executes onIndex.
```

### View and HTML

The ```$view``` object keeps the value set in the
controller. The values can be accessed as an array;
and the values are htmlspecialchars-ized.

```php
echo $view['title'];        // shows 'Got A Data'
$data = $view->get('data'); // get the data
echo $view->getPass();      // outputs hidden tags for id
$view->is( '_method', 'get' );
```

to get the raw data, use ```get``` method.

FYI, ```is``` method is also available.

```php
$view->is( '_current_method', 'get' );  // check the current method.
$view->is( '_method', 'get' );          // check the next method.
```


Advanced Features
-----------------

### error handling using $view object

To handle errors in the controller, use ```critical```,
 ```error```, or ```message``` inside the Controller to
 manage the errors.

```php
class MyController extends ControllerAbstract {
    public function onGet($id) {
        if( !$id ) {
            $this->critical( 'no id!' ); // throws an exception.
        }
        if( !$data = $this->dao->find($id) ) {
            $this->error( 'no such id: '.$id ); // set error message.
        } else {
            $this->message( 'found a data!' );  // show this message.
        }
        return [ 'title' => 'Got A Data!' ];
    }
}

// .... and in the php script...

$view = $app->execute('get');

echo $view->alert();
if( $view->isCritical() ) {
    // do nothing?
} elseif( $view->isError() ) {
    // do something?
} else {
    // do show some data?
}
```

Use ```alert``` method to display the messages.
 Use different style and class if there is an error.
 Use ```is{Critical|Error}``` to check the error status.



### C.S.R.F. Token

Generates token for Cross Site Resource Forgeries (CSRF).
This is a sample code to be used in the beginController
method.

```php
class MyController extends ControllerAbstract
    public function beginController( $method )
    {
        parent::beginController( $method );
        if( in_array( $method, [ 'add', 'mod', 'del' ] ) ) {
            $this->pushToken();
        }
        elseif( in_array( $method, [ 'post', 'put', 'delete' ] ) ) {
            if( !$this->verifyToken() ) {
                throw new \RuntimeException('cannot reload.' );
            }
        }
    }
}
/// in html
$view->getPass(); // トークンも一緒に出力される。
```

Ah, well, the beginController method is a method
that is called always before the execution.


### flash messages

Use ```flash{Message|Error}``` to set flash messages
in the session data, and use ```setFlashMessage()```
method to retrieve the message in the next page.
The flash message is thrown out if not used.

```php
class MyController extends ControllerAbstract
    public function onPut($id) {
        if( $this->dao->update( $data ) {
            $this->flashMessage( 'updated done!' );
        } else {
            $this->flashError( 'Ooops, not updated!!!' );
        }
        $this->location( '.../' ); // to onGet.
    }
    public function onGet($id) {
        $this->setFlashMessage();
    }
}
/// in html
echo $view->alert(); // shows the flash message.
```

### automatic view setup based on HTTP method

A lot of HTML values, such as title, breadcrumbs,
and button names, can be determined based on the
http method. So, there is a feature to do that
automatically.

```php
class MyController extends ControllerAbstract
    protected $currentView = array(
        // get data view
        'modForm'    => [
            '_method'        => 'put',
            '_buttonValue'   => 'modify data',
            '_subButtonType' => 'reset',
            'curr_title'     => 'Modification Form',
        ],
        'put'     => [
            '_method'        => 'index',
            '_buttonValue'   => 'list data',
            '_subButtonType' => 'none',
            'curr_title'     => 'update complete',
        ],
    );
    public function beginController( $method ) {
        $this->setCurrentMethod( $method );
    }
}
```

The automation takes place in the beginController
method using setCurrentMethod method. Please write
your own code if beginController is overloaded.

