# API文档

## 授权控制

授权控制，token等验证机制，可以在`lib/accessControl.php`中定义。

## 请求方式

使用POST方式提交，使用JSON传输数据，格式如下：

```
    {
        "requestMethod": 请求的API方法,
        ...     //附加数据
    }    
```

## 响应

`application/json`，成功操作无返回数据的，返回：

```
    {
        "code": 0,
        "msg": "success"
    }
```

任何操作失败返回：

```
    {
        "code": -1,
        "msg": "fail"
    }
```

请求数据的成功返回所请求的数据。

## API列表

| API方法  | 请求参数  |
|---       |---       |
| getWaiterList  |    |
| waiterSendMessage | waiterId sessionId type content |
| newWaiter | data |
| deleteWaiter | waiterId |
| newSession | userId addition|
| getUserHistorySession | userId [offset] [num] |
| getWaiterOpenSession | waiterId |
| getSessionData | sessionId |
| closeSession | sessionId |
| userSendMessage | userId sessionId type content |
| sessionSetWaiter | sessionId waiterId |
| getSessionNewestMessages | sessionId |
| getSessionLastMessages | sessionId [num] |
| getSessionAddition | sessionId |
| getUserLastSession | userId |

