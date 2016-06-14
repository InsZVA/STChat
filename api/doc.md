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

| API方法  | 请求参数  | 测试状态 |
|---       |---       | OK |
| getWaiterList  |    | OK |
| waiterSendMessage | waiterId sessionId type content | OK |
| newWaiter | data(must include _id(int)) | OK |
| deleteWaiter | waiterId | OK |
| newSession | userId addition| OK |
| getUserHistorySessions | userId [offset] [num] | OK |
| getWaiterOpenSessions | waiterId | OK |
| getSessionData | sessionId | OK |
| closeSession | sessionId | OK |
| userSendMessage | userId sessionId type content | OK |
| sessionSetWaiter | sessionId waiterId | OK |
| getSessionNewestMessages | sessionId | OK |
| getSessionLastMessages | sessionId [num] | OK |
| getSessionAddition | sessionId | OK |
| getUserLastSession | userId | OK |
| getReadySessions | | OK |

## 注意

`user._id`和`waiter._id`是整数，`session._id`是hash串，`newWaiter`调用时需指定`_id`
