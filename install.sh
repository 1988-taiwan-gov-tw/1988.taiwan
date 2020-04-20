#!/usr/bin/env bash
#請執行此此檔案，系統會自動建立子目錄 1988dev 
#需先建立一個空白資料庫
clear


# 輸入網站資料
echo "請輸入存放1988dev目錄的www絕對路徑，路徑不包含 1988dev 目錄"
read -p "例如: /home/web/www: " root_path
read -p "請輸入空白已建立的資料庫名稱 DB NAME: " dbname
read -p "請輸入資料庫使用者名稱 DB USER: " dbuser
read -p "請輸入資料庫密碼 DB Password: " dbpass


# 移到www絕對路徑
cd $root_path
# 建立子目錄 - 1988dev
mkdir -p 1988dev
# 移到子目錄 
cd 1988dev


# download file and unzip
curl -sS http://1988taiwan.pro9.designworks.tw/1988dev.zip > 1988dev.zip
unzip 1988dev.zip
rm 1988dev.zip

# 修改wp-config.php 連線設定
sudo chmod 664 wp-config.php
sed -i-e "s/1988dev/$dbname/g" wp-config.php 
sed -i-e "s/root/$dbuser/g" wp-config.php
sed -i-e "s/'DB_PASSWORD', ''/'DB_PASSWORD', '$dbpass'/g" wp-config.php
rm wp-config.php-e


# import sql to a exists mysql
mysql --user=$dbuser --password=$dbpass  $dbname < db.sql
rm db.sql


echo "後台網址 : http://localhost/1988dev/"
echo "後台管理網址 : http://localhost/1988dev/wp-admin/"
echo "管理者帳號 : admin"
echo "管理者密碼: admin"