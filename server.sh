#!/bin/bash

#启动脚本
processFile="src/start.php"


#不同的系统，进程标识不同,主要是mac机器不支持进程重命名
if [ "$(uname)" == "Darwin" ]; then
    # Do something under Mac OS X platform
    processMark=$processFile;
        
elif [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then
    # Do something under GNU/Linux platform
    processMark=" php: swoole-bot";

elif [ "$(expr substr $(uname -s) 1 10)" == "MINGW32_NT" ]; then
    # Do something under Windows NT platform
    printf "not support in windows \r\n"
    exit
fi



function start(){
    echo 'starting swooler-bot server...'
    # 删除session目录
    rm -rf log/session/*
    # 启动服务
    php $processFile  >> log/server.log 2>&1

    sleep 2
    # 修改目录权限，让PHP-FPM可以访问，用户浏览下预览二维码
    chmod -R 777 log/session/
    

    printf $?
    if [ $? == 0 ]; then
        printf "\server start OK\r\n"
        return 0
    else
        printf "\server start FAIL\r\n"
        return 1
    fi
}

function stop(){

    $(ps aux  | grep "$processMark" |grep -v "grep "| awk '{print $2}'    | xargs  kill -9) 

    PROCESS_NUM2=$(ps aux  | grep "$processMark" |grep -v "grep "| awk '{print $2}'   | wc -l )    
    if [ $PROCESS_NUM2 == 0 ]; then
        printf "\server stop OK\r\n"
        return 0
    else
        printf "\server stop FAIL\r\n"
        return 1
    fi

}


case $1 in 
    
    start )
        stop
        sleep 1
        start
    ;;
    stop)
        stop
    ;;
    restart)
        stop
        sleep 1
        start
    ;;

    *)
        start
    ;;
esac