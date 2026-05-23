

# 一、macos 安装 php mysql nginx

## **1. 安装 Homebrew**

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

安装完成后执行：

```bash
brew doctor
brew update
```

确认 brew 路径：

```bash
brew --prefix
```

后面我用变量表示：

```bash
BREW_PREFIX=$(brew --prefix)
echo $BREW_PREFIX
```

------

## **2. 安装 PHP、MySQL、Nginx**

```bash
brew install php mysql nginx
```

Homebrew 当前 MySQL 公式安装命令是 `brew install mysql`。 

检查版本：

```bash
php -v
mysql --version
nginx -v
```

------

## **3. 启动服务**

```bash
brew services start php
brew services start mysql
brew services start nginx
```

查看状态：

```bash
brew services list
```

停止或重启：

```bash
brew services stop nginx
brew services restart nginx
brew services restart php
brew services restart mysql
```

------

## **4. 初始化 MySQL**

先执行安全初始化：

```bash
mysql_secure_installation
```

然后登录：

```bash
mysql -u root -p
```

创建一个本地开发数据库和用户示例：

```sql
CREATE DATABASE test_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'dev'@'localhost' IDENTIFIED BY 'dev123456';

GRANT ALL PRIVILEGES ON test_db.* TO 'dev'@'localhost';

FLUSH PRIVILEGES;
```

测试登录：

```bash
mysql -u dev -p test_db
```

------

## **5. 配置 Nginx 支持 PHP**

先找到 Nginx 配置目录：

```bash
nginx -t
```

通常配置文件在：

Apple Silicon：

```bash
/opt/homebrew/etc/nginx/nginx.conf
```

Intel Mac：

```bash
/usr/local/etc/nginx/nginx.conf
```

编辑配置：

```bash
nano $(brew --prefix)/etc/nginx/nginx.conf
```

找到 `server` 配置，建议改成下面这样：

```nginx
server {
    listen 8080;
    server_name localhost;

    root /opt/homebrew/var/www;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

如果你是 Intel Mac，把：

```nginx
root /opt/homebrew/var/www;
```

改成：

```nginx
root /usr/local/var/www;
```

也可以用命令自动看目录：

```bash
echo "$(brew --prefix)/var/www"
```

------

## **6. 创建测试 PHP 文件**

```bash
mkdir -p $(brew --prefix)/var/www

cat > $(brew --prefix)/var/www/index.php <<'EOF'
<?php
phpinfo();
EOF
```

检查 Nginx 配置：

```bash
nginx -t
```

重启服务：

```bash
brew services restart php
brew services restart nginx
```

浏览器打开：

```text
http://localhost:8080
```

看到 `phpinfo()` 页面就说明 PHP + Nginx 通了。

------



# 二、Git SSH 的配置

1.查看本地key文件

```bash
ls -al ~/.ssh
```

看有没有类似这些文件：，**如果有 id_ed25519.pub，直接跳到 5 . 把公钥添加到 GitHub**

```text
id_ed25519
id_ed25519.pub
id_rsa
id_rsa.pub
```

如果有 `.pub` 文件，说明有公钥。

---

**2. 测试当前 SSH 是否能连 GitHub**

```bash
ssh -T git@github.com
```

如果还是：

```text
Permission denied (publickey).
```

说明 GitHub 没有绑定你的公钥，或者 ssh 没用对 key。

---

**3. 如果没有 SSH key，生成一个**

推荐用 Ed25519：

```bash
ssh-keygen -t ed25519 -C "你的邮箱@example.com"
```

一路回车即可。生成后会有：

```text
~/.ssh/id_ed25519
~/.ssh/id_ed25519.pub
```

---

**4. 启动 ssh-agent 并添加 key**

```bash
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519
```

如果你用的是 macOS，也可以用：

```bash
ssh-add --apple-use-keychain ~/.ssh/id_ed25519
```

---



**5. 把公钥添加到 GitHub**

复制公钥：

```bash
cat ~/.ssh/id_ed25519.pub
```

然后打开 GitHub：

GitHub → Settings → SSH and GPG keys → New SSH key

把复制出来的内容粘进去，保存。

公钥长这样，注意复制整行：

```text
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAA... 你的邮箱@example.com
```

不要复制私钥 `id_ed25519`，只复制 `.pub`。

---

**6. 再测试 SSH**

```bash
ssh -T git@github.com
```

成功时通常会看到类似：

```text
Hi your-username! You've successfully authenticated, but GitHub does not provide shell access.
```

这就说明 SSH 认证通了。



# 三、本地文件git初始化



## **1. 进入本地项目目录**

```bash
cd /你的项目路径
```

例如：

```bash
cd /opt/homebrew/var/www
```

## **2. 初始化 Git 仓库**

如果本地项目还没有 `.git` 目录，执行：

```bash
git init
```

## **3. 查看当前文件状态**

```bash
git status
```

## **4. 添加文件到暂存区**

添加全部文件：

```bash
git add .
```



## **5. 提交到本地仓库**

```bash
git commit -m "initial commit"
```

如果提示没有配置用户名和邮箱，先执行：

```bash
git config --global user.name "你的名字"
git config --global user.email "你的邮箱"
```

然后重新提交：

```bash
git commit -m "initial commit"
```

## **6. 关联远程仓库**

```bash
git remote add origin 远程仓库地址
```

例如 HTTPS：

```bash
git remote add origin https://github.com/xxx/xxx.git
```

或 SSH：

```bash
git remote add origin git@github.com:xxx/xxx.git
```

查看是否关联成功：

```bash
git remote -v
```

## 7.先 pull，再 push

```
git pull origin main --rebase
```



## **8. 推送到远程仓库**

如果远程默认分支是 `main`：

```bash
git branch -M main
git push -u origin main
```



------

## **常见完整命令**

```bash
cd /你的项目路径
git init
git add .
git commit -m "initial commit"
git remote add origin https://github.com/xxx/xxx.git
git branch -M main
git push -u origin main
```

## **后续更新代码**

以后修改代码后，只需要：

```bash
git add .
git commit -m "更新说明"
git push
```

## **如果已经关联过远程仓库**

查看远程地址：

```bash
git remote -v
```

修改远程地址：

```bash
git remote set-url origin 新的仓库地址
```

删除后重新添加：

```bash
git remote remove origin
git remote add origin 新的仓库地址
```

如果你是上传到 **GitHub / Gitee / GitLab**，流程基本一样，只是远程仓库地址不同。



# 四、MACos 去掉隐藏文件



## **打开 终端**

输入下面两行内容显示隐藏文件：

```bash
defaults write com.apple.finder AppleShowAllFiles -bool true
killall Finder
```

恢复隐藏：

```bash
defaults write com.apple.finder AppleShowAllFiles -bool false
killall Finder
```

如果你是为了查看 Git 项目的 `.git` 文件夹，推荐直接在 Finder 里用快捷键 `⌘ + ⇧ + .`。

