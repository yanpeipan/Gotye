Gotye
=====

[亲加通讯云后台 API接口](http://www.gotye.com.cn/docs/ime/restapi.html)

# 安装

```
composer require yanpeipan/gotye:dev-master
```

# 使用

```
require 'vendor/autoload.php';
$gotye = new Yan\gotye($email, $devpwd, $appkey);
```

用户管理,群管理可直接使用形参

```
$gotye->GetUserlist(0, 20);
```

其他接口需要包裹在数组里

```
$gotye->SetupKeyword(array('setup_type' => 1, 'key_word' => '敏感词1,敏感词2'));
```
