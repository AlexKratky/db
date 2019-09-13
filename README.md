# db

Simple database wrapper

### Installation

`composer require alexkratky/db`

### Usage

```php
db::connect($host, $user, $pass, $db);
$user = db::select("SELECT * FROM users WHERE ID = ?", array($id));
echo $user["name"];
```