#!/usr/bin/env bash

dir=`dirname "$0"`
dir=`realpath "$dir"`
dir="$dir/storage/articles"
file="$dir/$1"
txt=$(basename -- "$file" .doc)
txt=$(basename -- "$txt" .docx)
txt="$dir/$txt.txt"
export HOME=/tmp
res=$(/usr/local/bin/libreoffice --writer --nologo --norestore --invisible --nolockcheck --nodefault --headless --convert-to txt $file --outdir $dir)
words=$(wc -w "$txt")
echo $words
