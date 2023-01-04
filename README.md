# Monitor

## 说明
常驻脚本监控，可根据需要配置监控信号/执行时间/执行次数/代码更新。 

常驻脚本重启需要配合 `Supervisor` 或 `PM2` 等进程管理程序。

## 要求
1. PHP >= 7.1.0
2. ext-pcntl

## 安装
```shell
$ composer require mitoop/monitor
```

## 使用
参考 `tests` 文件夹下的例子