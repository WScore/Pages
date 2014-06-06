WScore.Pages
============

古き良き（悪しき）PHPコードのためのページ・コントローラー。

古いスタイルで書かれたコードを保守しなければいけない人（自分）が
少しでも楽になるようにと祈りつつ作成されたコンポーネントです。

### ライセンス

MIT License

### インストール

コンポーザーを使ってください。バージョンは「dev-master」しか
準備してありません。

```json
"require": {
    "wscore/pages":"dev-master"
}
```

簡単な使い方
----------

比較的普通だと思います。

1.   ページ用の ```Controller``` を作成し、オブジェクトを生成する。
2.   ```Dispatch`` でコントローラー内のメソッドを実行する。
3.   結果は ```View``` オブジェクトなので、適宜HTMLを構成する。

### 簡単なController

ControllerAbstractを継承して作成します。

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

HTTPメソードを元に、```onHttpMethodName``` を実行します。
例えば、HTTPメソッドが```get```なら```onGet```が実行されます。
HTTPメソッドの上書きは```_method```変数を使います。

メソッドの引数を指定すると、変数名と同じ値を$_REQUESTから
探して設定します。例の場合は```$_REQUEST['id']```の値に
なります。見つからない場合はnullが帰ります。

ビューに値を設定するには```set```を使います。
あるいは、最後に配列を返すことでも設定できます。

また、```pass```を使うと、次のリクエストに渡すための
hiddenタグに出力することが出来ます。

### Dispatchコード

ファクトリを使うと簡単です。

```php
$app = \WScore\Pages\Factory::getDispatch(
    new MyController( new MyDao() )
);
$view = $app->execute();
```

実行するメッソドを指定することも可能です。

```php
$view = $app->execute( 'index' ); // onIndexが実行される
```

### ViewとHTML作成

$viewはコントローラーで設定された値を保持しています。
配列としてアクセスするとhtmlspecialcharsをかけてから
値を返します。

```php
echo $view['title'];        // 'Got A Data'と表示する
$data = $view->get('data'); // データ配列を取得
echo $view->getPass();      // idなどをhiddenタグを出力
$view->is( '_method', 'get' );
```

直接データを取得するなら```get```を使います。

その他、```is```などもあります。

```php
$view->is( '_current_method', 'get' );  // 今のHTTPメソッドをチェック。
$view->is( '_method', 'get' );          // 次のメソッドをチェック。
```

高度な機能
--------

### $viewを使った簡易的なエラー処理

エラーが起きた時の処理です。コントローラー内で、
適宜```critical```, ```error```, ```message```を
使ってエラー状態を設定してください。

```php
class MyController extends ControllerAbstract {
    public function onGet($id) {
        if( !$id ) {
            $this->critical( 'no id!' ); // throws an exception.
        }
        if( !$data = $this->dao->find($id) ) {
            $this->error( 'no such id: '.$id );
        } else {
            $this->pass( 'id,' $id );
            $this->set(  'data', $data );
            $this->message( 'found a data!' );
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
    // do show some data
}
```

表示は```alert```を使います。エラーによりスタイル・クラスが
決まります。```is{Critical|Error}```でエラーが分かります。



### C.S.R.F.トークン

CSRF対策のためのトークンを生成＆確認します。
例えば、beginController内で使うコードを書いてみます。

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

えぇと、beginControllerメソッドは、とにかく最初に必ず
呼ばれるメソッドです。


### フラッシュ・メッセージ

フラッシュメッセージは```flash{Message|Error}```で設定し、
次の画面で```setFlashMessage()```で読み込みます。
読まなければ、メッセージは破棄されます。

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
