# 客服系统

## 运行环境

- PHP 7
- MongoDB

## 配置

`config/Config.php`中可以配置数据库地址以及数据库名。

## 业务场景

适用于需要客服和用户进行交流的环境，可以是用户发起交流，选择客服与其开始对话，每个用户同时最多有一个等待/开放状态的客服会话，
客服也可以关闭会话。用户可以查看以前的历史记录，客服同时可以和多个用户交流。

## 概念

1. 用户
用户数据属于外接，在`lib/User.php`中进行编辑，本系统内不涉及外接数据。

2. 会话
会话有三种状态，Ready状态下处理等待分配客服，Open状态下可以交谈，Close状态表示不可用。会话带有一个附加属性可以表征发起会话的原因
或者附带一个表征用户身份（可能是VIP？）的数据。

3. 客服
客服的数据存在本系统内，可以查询客服正在处理的会话。

## API

[API文档](api/doc.md)