# laravel-helper
### 助手函数
* authUser(): App\User | App\Models\User | null
    * 以ide友好方式获取认证后的用户（使用laravel自带的用户表）
  
* message(string $message, int $code = 1, int $status = 200, $jsonOption = JSON_UNESCAPED_UNICODE): JsonResponse
    * 纯消息响应
  
```php
  public function index()
  {
      return message('operation success');
  }
```
* success(arrayable $data, int $code = 2, int $status = 200, $jsonOption = JSON_UNESCAPED_UNICODE): JsonResponse
    * 数据响应，数据或者实现了arrayable接口的对象，如collection
```php
    public function index()
    {
        return success(User::all());
    }
```
  
* error(string $message, int $code = 0, int $status = 400, $jsonOption = JSON_UNESCAPED_UNICODE): JsonResponse
  - 错误消息响应
    
```php
    public function index()
    {
        return error('something went wrong');
    }
```

### 助手类

* Tools::alterEnv(array $env)    
  - 增加或者修改.env文本（将.env文件设为www用户php运行用户）若开启缓存将执行artisan config:cache命令
* Tools::imagesUrl(array $images)    
  - 在api资源中使用可以将json多图或者关联多图调用Storage::url补全路径

### 异常处理
* 捕获全局异常修改为统一响应格式, 全局http状态码皆为200    
```php
    [
        'message': 'error message',
        'code': 'exception->code()',
        'status': 'exception->getStatusCode() | 500'
    ]
```
* 单独处理表单验证    
```php
    [
        'message': '第一个验证不通过的错误信息',
        'errors': 'exception->errors() 所有的验证错误信息'
        'code': 'exception->code()',
        'status': '422'
    ]
```
* 用户认证处理，status 401
* Model Not Found处理， status 404

### 资源文件
* 表单验证汉化包    
```shell
 php artisan vendor:publish
```
  - 选择 weiwait/laravel-helper
