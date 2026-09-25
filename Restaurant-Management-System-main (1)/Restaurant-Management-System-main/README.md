# Restaurant-Management-System
# Cấu trúc thư mục `app/views/`(tham khảo)

```
app/views/
│
├── layouts/  #khi làm đến php thì sẽ có include, 
|   |          làm bằng html thì chịu khó chép vào  
│   ├── client/
│   │   ├── header.php
│   │   └── footer.php
│   └── admin/
│       ├── header.php
│       └── footer.php
│
├── client/
│   │
│   ├── home/
│   │   └── index.php
│   │
│   ├── menu/
│   │   ├── list.php
│   │   └── detail.php
│   │
│   ├── promotion/
│   │   └── index.php
│   │
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   │
│   └── member/
│       ├── profile.php
│       └── edit-profile.php
│
└── admin/
    │
    ├── auth/
    │   └── login.php
    |
    ├── overview/
    │   └── login.php
    │
    ├── dashboard/
    │   ├── overview.php
    │   └── statistics.php
    │
    ├── menu/
    │   ├── list.php
    │   ├── create.php
    │   └── edit.php
    │
    ├── inventory/
    │   ├── list.php
    │   ├── create.php
    │   └── edit.php
    │
    ├── user/
    │   ├── list.php
    │   ├── create.php
    │   └── edit.php
    │
    ├── table/
    │   ├── list.php
    │   ├── create.php
    │   └── edit.php
    │
    ├── order/
    │   ├── list.php
    │   └── detail.php
    │
    └── voucher/
        ├── list.php
        ├── create.php
        └── edit.php
```