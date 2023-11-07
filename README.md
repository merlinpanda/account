
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


## 登录方式

- 邮箱/手机号 + 密码
- 邮箱/手机号 + 验证码
- APP扫码登录
- 第三方OAuth登录
  - 微信
    - 公众号登录
    - 扫码
    - 小程序
  - 抖音登录
  - QQ扫码登录
  - github登录
  - facebook登录
  - x登录
  - ...

