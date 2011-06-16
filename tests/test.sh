#!/bin/sh

#
# File: FullName.sh
# @see $BF~Lg(BUNIX$B%7%'%k%W%m%0%i%_%s%0(B
#
FullName() {
        #
        # $BL>>N(B
        #    FullName - $B%U%!%$%k$d%G%#%l%/%H%j$N40A4%Q%9L>$rJV$9(B
        # $B=q<0(B
        #    FullName filename | directory
        #
        # $B2r@b(B
        #    $B;XDj$5$l$?%U%!%$%k$d%G%$%l%/%H%j$r40A4%Q%9L>$GJV$9(B
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
