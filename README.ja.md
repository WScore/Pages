WScore.Pages
============

古き良き（悪しき）PHPコードのためのページ・コントローラー。

古いスタイルで書かれたコードを保守しなければいけない人（自分）が
少しでも楽になるようにと祈りつつ作成されたコンポーネントです。

例えば、次のように

`http://example.com/example.php`

直接PHPにアクセスする場合でも、コントローラを使った自由な開発をすることが目的です。

### ライセンス

MIT License

### インストール

PHP5.6が対象です。 
まだ~~ベータ~~α版のみです。

```sh
composer require "wscore/pages: ^0.1"
```

### デモ

```sh
git clone https://github.com/WScore/Pages
cd Pages
composer install
cd public
php -S localhost:8000 index.php
```

ブラウザーで`localhost:8000`にアクセスしてください。

簡単な使い方
----------

比較的普通だと思います。

1. ページ用の `Controller` を作成し、オブジェクトを生成する。
2. `Dispatch` でコントローラーを実行する。
3. HTMLページをビュー用ディレクトリ以下に作成する。

### Controllerを作成する

`ControllerAbstract`を継承して作成します。

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

### コントローラーを実行する

PHPファイル内で、Controllerを実行（`Dispatch`）します。


```php
use Laminas\Diactoros\ServerRequestFactory;
use WScore\Pages\Dispatch;

$request = ServerRequestFactory::fromGlobals();
$controller = new DemoController();

$view = Dispatch::create($controller, __DIR__ . '/views')
    ->handle($request);
$view->render();
```

`Dispatch`でセッション管理やCSRFトークンチェックなどを行ってます。

実行するコントローラーのメソッド名は、下記のいずれかから決定します。

- HTTPメソッド名から、
- GET/POSTの「act」の値から、

`onMethod`が呼ばれます。
普通にアクセスすると「`onGet`」が呼ばれます。
フォームでポストすれば「`onPost`」です。

### Viewファイル

サンプルコードの「`__DIR__ . '/views'`」がビュー用のディレクトリになります。

ここに「`user.php`」を作成します。

```php
use WScore\Pages\View\Data;
/** @var Data $_view */

$user = $_view->get('user');
?>
<!-- show user -->
```

- `$this`はコントローラ・Dispatchから戻るオブジェクトです（`PaveView`）。
- `$view`は描画用パラメーターなどを保持しているオブジェクトです（`Data`）。

その他の機能
----------

### CSRFトークン

`$view`オブジェクトからCSRFトークンを出力します。

```php
use WScore\Pages\View\Data;
/** @var Data $_view */

echo $_view->makeCsRfToken();
```

ポストされた場合は、必ずCSRFトークンをチェックします。
チェックに失敗した場合は、Criticalエラーとなります。

### Criticalエラー

`PageView`の特殊なエラーです。

```php
use WScore\Pages\PageView;
/** @var PageView $view */

if ($view->isCritical()) {
    $view->setRender('critical.php');
}
$view->render();
```

- [ ] コントローラーを実行する際に例外をキャッチしたら、Criticalエラーとする。

### メッセージとフラッシュ

コントローラーの`message`および`error`でメッセージを指定できます。

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

ビューPHPで、表示できます。

```php
use WScore\Pages\PageView;
/** @var PageView $view */

echo $view->alert();
```

### フラッシュメッセージ

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
```

メッセージと同じPHPコードで、表示できます。


### XSS対策

t.b.w

### HTMLフォーム

t.b.w
