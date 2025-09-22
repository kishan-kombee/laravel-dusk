class InitFromMysqlSchema < ActiveRecord::Migration[7.0]
  def change
    create_table :activity_logs do |t|
      t.string :log_name
      t.timestamps
    end
    create_table :carts do |t|
      t.integer :user_id, default: "NULL"
      t.integer :product_id, default: "NULL"
      t.integer :quantity, default: "NULL"
      t.integer :created_by, default: "NULL"
      t.integer :updated_by, default: "NULL"
      t.integer :deleted_by, default: "NULL"
      t.datetime :created_at, default: "NULL"
      t.datetime :updated_at, default: "NULL"
      t.datetime :deleted_at, default: "NULL"
      t.string :PRIMARY
      t.timestamps
    end
    create_table :categories do |t|
      t.integer :parent_id, null: false, default: 0
      t.string :name
      t.timestamps
    end
    create_table :category_products do |t|
      t.integer :product_id, default: "NULL"
      t.integer :category_id, default: "NULL"
      t.integer :parent_id, default: "NULL"
      t.timestamps
    end
    create_table :email_formats do |t|
      t.integer :type, null: false
      t.string :label
      t.timestamps
    end
    create_table :email_histories do |t|
      t.string :to_email
      t.timestamps
    end
    create_table :email_templates do |t|
      t.integer :type, null: false
      t.string :label
      t.timestamps
    end
    create_table :failed_jobs do |t|
      t.string :uuid
      t.timestamps
    end
    create_table :homebanners do |t|
      t.string :name
      t.timestamps
    end
    create_table :import_csv_logs do |t|
      t.string :file_name
      t.timestamps
    end
    create_table :job_batches do |t|
      t.string :id
      t.timestamps
    end
    create_table :jobs do |t|
      t.string :queue
      t.timestamps
    end
    create_table :migrations do |t|
      t.string :migration
      t.timestamps
    end
    create_table :milestone_histories do |t|
      t.integer :user_id, null: false
      t.integer :milestone_id, null: false
      t.integer :order_id, null: false
      t.string :title
      t.timestamps
    end
    create_table :milestones do |t|
      t.string :title
      t.timestamps
    end
    create_table :oauth_access_tokens do |t|
      t.string :id
      t.timestamps
    end
    create_table :oauth_auth_codes do |t|
      t.string :id
      t.timestamps
    end
    create_table :oauth_clients do |t|
      t.bigint :user_id, default: "NULL"
      t.string :name
      t.timestamps
    end
    create_table :oauth_personal_access_clients do |t|
      t.bigint :client_id, null: false
      t.datetime :created_at, default: "NULL"
      t.datetime :updated_at, default: "NULL"
      t.string :PRIMARY
      t.timestamps
    end
    create_table :oauth_refresh_tokens do |t|
      t.string :id
      t.timestamps
    end
    create_table :order_products do |t|
      t.integer :order_id, null: false
      t.integer :product_id, null: false
      t.string :product_name
      t.timestamps
    end
    create_table :order_statuses do |t|
      t.integer :order_id, null: false
      t.string :order_status
      t.timestamps
    end
    create_table :orders do |t|
      t.integer :user_id, null: false
      t.integer :quantity, default: "NULL"
      t.decimal :gst
      t.timestamps
    end
    create_table :password_reset_tokens do |t|
      t.string :email
      t.timestamps
    end
    create_table :permission_role do |t|
      t.integer :permission_id, default: "NULL"
      t.integer :role_id, default: "NULL"
      t.timestamps
    end
    create_table :permissions do |t|
      t.string :name
      t.timestamps
    end
    create_table :product_galleries do |t|
      t.integer :product_id, null: false
      t.string :gallery
      t.timestamps
    end
    create_table :product_histories do |t|
      t.integer :id, null: false
      t.string :name
      t.timestamps
    end
    create_table :products do |t|
      t.string :name
      t.timestamps
    end
    create_table :pulse_aggregates do |t|
      t.integer :bucket, null: false
      t.string :period, null: false
      t.string :type
      t.timestamps
    end
    create_table :pulse_entries do |t|
      t.integer :timestamp, null: false
      t.string :type
      t.timestamps
    end
    create_table :pulse_values do |t|
      t.integer :timestamp, null: false
      t.string :type
      t.timestamps
    end
    create_table :roles do |t|
      t.string :name
      t.timestamps
    end
    create_table :sms_templates do |t|
      t.string :type
      t.timestamps
    end
    create_table :telescope_entries do |t|
      t.bigint :sequence, null: false
      t.string :uuid
      t.timestamps
    end
    create_table :telescope_entries_tags do |t|
      t.string :entry_uuid
      t.timestamps
    end
    create_table :telescope_monitoring do |t|
      t.string :tag
      t.timestamps
    end
    create_table :users do |t|
      t.integer :role_id, default: "NULL"
      t.string :is_salesman
      t.timestamps
    end
    add_index :carts, :user_id
    add_index :carts, :product_id
    add_index :categories, :parent_id
    add_index :category_products, :product_id
    add_index :category_products, :category_id
    add_index :category_products, :parent_id
    add_index :milestone_histories, :user_id
    add_index :milestone_histories, :milestone_id
    add_index :milestone_histories, :order_id
    add_index :oauth_clients, :user_id
    add_index :oauth_personal_access_clients, :client_id
    add_index :order_products, :order_id
    add_index :order_products, :product_id
    add_index :order_statuses, :order_id
    add_index :orders, :user_id
    add_index :permission_role, :permission_id
    add_index :permission_role, :role_id
    add_index :product_galleries, :product_id
    add_index :users, :role_id
  end
end