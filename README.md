```mermaid

erDiagram
  USERS {
    bigint id PK
    string name
    string email
    string password
    string image
    string postal_code
    string address
    string building
    timestamp email_verified_at
    timestamps
  }

  ITEMS {
    bigint id PK
    bigint user_id FK
    string name
    text description
    int price
    string brand
    string condition
    string img_url
    timestamps
  }

  ITEM_IMAGES {
    bigint id PK
    bigint item_id FK
    string path
    boolean is_main
    timestamps
  }

  CATEGORIES {
    bigint id PK
    string name
    timestamps
  }

  CATEGORY_ITEM {
    bigint id PK
    bigint item_id FK
    bigint category_id FK
    timestamps
  }

  COMMENTS {
    bigint id PK
    bigint user_id FK
    bigint item_id FK
    text body
    timestamps
  }

  FAVORITES {
    bigint id PK
    bigint user_id FK
    bigint item_id FK
    timestamps
  }

  LIKES {
    bigint id PK
    bigint user_id FK
    bigint item_id FK
    timestamps
  }

  PURCHASES {
    bigint id PK
    bigint user_id FK
    bigint item_id FK
    int price
    string postal_code
    string address
    string building
    tinyint payment_method
    tinyint status
    timestamps
  }

  USERS ||--o{ ITEMS : sells
  USERS ||--o{ COMMENTS : writes
  USERS ||--o{ FAVORITES : likes
  USERS ||--o{ LIKES : likes_legacy
  USERS ||--o{ PURCHASES : buys

  ITEMS ||--o{ ITEM_IMAGES : has
  ITEMS ||--o{ COMMENTS : has
  ITEMS ||--o{ FAVORITES : liked_by
  ITEMS ||--o{ LIKES : liked_by_legacy
  ITEMS ||--o{ PURCHASES : purchased_once
  ITEMS }o--o{ CATEGORIES : via_CATEGORY_ITEM
```
