Assuming you have implemented the user login and register for your website.

You'll need to create the database named password_reset which serve the purpose of saving generated tokens
Table attributes will be as follow
id 	(Integer) Primary Key)
email	(var 255)
token	(var(255)
expire_time (datetime)


and update the source code with your approperiate variable names
You'll need to update the database table and colunm names inside the source code for admin and user.

you will need to download "mailhog" from github (https://github.com/mailhog/MailHog/releases)
run mailhog on your machine 

mailhog run on port 8025
type (http://localhost:8025/) on your browser to access mailhog

open localhost and go to forgot password
Type an email that was registered as either admin or user on your phpmyadmin
go to mailhog, mailhog will catch the email linked with the token,
click the link and update the password

//-------
the code still need to be updated, the expire_time doesn't do anything
the token should be destroyed after expire
still need to work on it
//--------


