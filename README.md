### ENDPOINTS

- localhost:8080/register.php
  request body

```
{
    "email": "email@email.com",
    "password": "password",
    "cpassword" : "password",
    "lname: "John",
    "fname": "Doe"

}
```

- localhost:8080/login.php
  request body

```
{
    "email": "email@email.com",
    "password": "password",

}
```

- localhost:8080/getblogsadmin/blogs
  Get all blogs for admin users

- localhost:8080/getblogs.php/blogs
  Get all published blogs

- localhost:8080/getblogs.php/blogs/{id}
  Get single blogs

- localhost:8080/createblog.php

```
{
    "title" : "PHP Explained",
    "category_id" : "2",
    "content": "PHP"
}

```

- localhost:8080/updateblog.php/blogs/{id}

```
{
    "title" : "PHP Explained",
    "category_id" : "2",
    "content": "PHP",
    "published: "1" or "0"
}

```
