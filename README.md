# 1988顧台灣

## 開發者資訊
- 專案採 Wordpress 開源碼開發，使用版本為 5.4  

## 你能如何貢獻：
- 提供正確資訊校稿
- 提供架構規劃建議
- 提供設計介面建議
- 提供優化功能建議
- 協助翻譯

## 簡易安裝指南
Wordpress 
- 下載：https://tw.wordpress.org/download/
- 安裝指南：https://wordpress.org/support/article/how-to-install-wordpress/
- 建議採用執行 PHP 7.3 及 MySQL 5.6 或更新版本的伺服器，資料庫亦可採用 MariaDB 10.1 或更新版本。
- 也建議在 Apache 或 Nginx 這兩種最適合執行 WordPress 的網頁伺服器中擇一使用，但是也可以使用其他網頁伺服器。
 
## 主題採用：Astra 
- 下載： https://wpastra.com
- Astra is 100 percent open source and developed transparently on GitHub. Anyone can join the development and contribute.

## 程式安裝步驟
### 預設資料
1. 網站路徑：http://localhost
2. 預設網站後台路徑：http://localhost/wp-admin
	- 管理帳號：admin
	- 管理密碼：admin
3. 資料庫名稱：1988dev
4. 資料庫使用者：root
5. 資料庫密碼：無


### 下載檔案
1. 下載 github 檔案在 www 環境下方根目錄下存檔。

### 資料庫設定
1. 請開啟 phpmyadmin 或資料庫管理介面，新增一個資料庫。
2. 點選「匯入」將下載檔案中的 db.sql 匯入。 

### 程式設定
1. 修改資料庫連線資訊：
	- 若資料庫名稱、使用者帳密非預設設定值時，請做以下修改：
	- 開啟根目錄下的 wp-config.php
	- 請將 1988dev 改成您設定的資料庫名稱。

		```位置 line 23：define( 'DB_NAME', '1988dev' ); ```
	- 請將 root 改成您設定的資料庫使用者。

		```位置 line 26：define( 'DB_USER', 'root' ); ```
	- 請在 '' 裡面填入您設定的資料庫密碼。

		```位置 line 29：define( 'DB_PASSWORD', '' ); ```


### 開始使用
1. 預設網站路徑：http://localhost
2. 預設網站後台路徑：http://localhost/wp-admin

### 修改網站路徑  
1. 修改網址
	- 請登入 網站後台路徑：http://localhost/wp-admin/options-general.php
	- 修改「WordPress 位址（URL）」和「網站位址（URL）」儲存

2. 將檔案全部移到指定目錄下

3. 開啟網頁瀏覽，若無法正常使用，請修改檔案
	- 開啟 .htaccess 檔案
	- 請將 / 改成您設定的子目錄名稱。

		```位置 line 7：RewriteBase /```
	- 請將 / 改成您設定的子目錄名稱。

		```位置 line 11：RewriteRule . /index.php [L]```

