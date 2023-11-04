
更改User Model
```
namespace App\Models;

use Merlinpanda\Account\Models\User as AccountUser;

class User extends AccountUser {
    // ...
}
```


## 登录

### 登录前

### 登录中

### 登录后
#### Actions
生成token BuildJwtToken

#### Event
触发OnFinishedLogin

- （仅当异常登录成功时）发送登录邮件


