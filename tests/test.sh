#!/bin/sh

#
# File: FullName.sh
# @see 入門UNIXシェルプログラミング
#
FullName() {
        #
        # 名称
        #    FullName - ファイルやディレクトリの完全パス名を返す
        # 書式
        #    FullName filename | directory
        #
        # 解説
        #    指定されたファイルやデイレクトリを完全パス名で返す
        # 
        _CWD=`pwd`

        if [ $# -ne 1 ]; then
                echo "Usage FullName filename | directory" 1>&2
                exit 1
        fi

        if [ -d $1 ]; then
                cd $1
                echo `pwd`
        elif [ -f $1 ]; then
                cd `dirname $1`
                echo `pwd`/`basename $1`
        else
                echo $1
        fi

        cd $_CWD
}

fullpath=`FullName $0`
fullpath=`dirname $fullpath`
pear run-tests ${fullpath}/*
