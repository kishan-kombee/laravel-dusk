class CreateActivityLogs < ActiveRecord::Migration[7.0]
  def change
    create_table :activity_logs do |t|
      t.string :log_name
      t.timestamps
    end
  end
end

class CreateCarts < ActiveRecord::Migration[7.0]
  def change
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
  end
end

class CreateCategories < ActiveRecord::Migration[7.0]
  def change
    create_table :categories do |t|
      t.integer :parent_id, null: false, default: 0
      t.string :name
      t.timestamps
    end
  end
end

class CreateCategoryProducts < ActiveRecord::Migration[7.0]
  def change
    create_table :category_products do |t|
      t.integer :product_id, default: "NULL"
      t.integer :category_id, default: "NULL"
      t.integer :parent_id, default: "NULL"
      t.timestamps
    end
  end
end

class CreateEmailFormats < ActiveRecord::Migration[7.0]
  def change
    create_table :email_formats do |t|
      t.integer :type, null: false
      t.string :label
      t.timestamps
    end
  end
end

class CreateEmailHistories < ActiveRecord::Migration[7.0]
  def change
    create_table :email_histories do |t|
      t.string :to_email
      t.timestamps
    end
  end
end

class CreateEmailTemplates < ActiveRecord::Migration[7.0]
  def change
    create_table :email_templates do |t|
      t.integer :type, null: false
      t.string :label
      t.timestamps
    end
  end
end

class CreateFailedJobs < ActiveRecord::Migration[7.0]
  def change
    create_table :failed_jobs do |t|
      t.string :uuid
      t.timestamps
    end
  end
end

class CreateHomebanners < ActiveRecord::Migration[7.0]
  def change
    create_table :homebanners do |t|
      t.string :name
      t.timestamps
    end
  end
end

class CreateImportCsvLogs < ActiveRecord::Migration[7.0]
  def change
    create_table :import_csv_logs do |t|
      t.string :file_name
      t.timestamps
    end
  end
end

class CreateJobBatches < ActiveRecord::Migration[7.0]
  def change
    create_table :job_batches do |t|
      t.string :id
      t.timestamps
    end
  end
end

class CreateJobs < ActiveRecord::Migration[7.0]
  def change
    create_table :jobs do |t|
      t.string :queue
      t.timestamps
    end
  end
end

class CreateMigrations < ActiveRecord::Migration[7.0]
  def change
    create_table :migrations do |t|
      t.string :migration
      t.timestamps
    end
  end
end

class CreateMilestoneHistories < ActiveRecord::Migration[7.0]
  def change
    create_table :milestone_histories do |t|
      t.integer :user_id, null: false
      t.integer :milestone_id, null: false
      t.integer :order_id, null: false
      t.string :title
      t.timestamps
    end
  end
end

class CreateMilestones < ActiveRecord::Migration[7.0]
  def change
    create_table :milestones do |t|
      t.string :title
      t.timestamps
    end
  end
end

class CreateOauthAccessTokens < ActiveRecord::Migration[7.0]
  def change
    create_table :oauth_access_tokens do |t|
      t.string :id
      t.timestamps
    end
  end
end

class CreateOauthAuthCodes < ActiveRecord::Migration[7.0]
  def change
    create_table :oauth_auth_codes do |t|
      t.string :id
      t.timestamps
    end
  end
end

class CreateOauthClients < ActiveRecord::Migration[7.0]
  def change
    create_table :oauth_clients do |t|
      t.bigint :user_id, default: "NULL"
      t.string :name
      t.timestamps
    end
  end
end

class CreateOauthPersonalAccessClients < ActiveRecord::Migration[7.0]
  def change
    create_table :oauth_personal_access_clients do |t|
      t.bigint :client_id, null: false
      t.datetime :created_at, default: "NULL"
      t.datetime :updated_at, default: "NULL"
      t.string :PRIMARY
      t.timestamps
    end
  end
end

class CreateOauthRefreshTokens < ActiveRecord::Migration[7.0]
  def change
    create_table :oauth_refresh_tokens do |t|
      t.string :id
      t.timestamps
    end
  end
end

class CreateOrderProducts < ActiveRecord::Migration[7.0]
  def change
    create_table :order_products do |t|
      t.integer :order_id, null: false
      t.integer :product_id, null: false
      t.string :product_name
      t.timestamps
    end
  end
end

class CreateOrderStatuses < ActiveRecord::Migration[7.0]
  def change
    create_table :order_statuses do |t|
      t.integer :order_id, null: false
      t.string :order_status
      t.timestamps
    end
  end
end

class CreateOrders < ActiveRecord::Migration[7.0]
  def change
    create_table :orders do |t|
      t.integer :user_id, null: false
      t.integer :quantity, default: "NULL"
      t.decimal :gst
      t.timestamps
    end
  end
end

class CreatePasswordResetTokens < ActiveRecord::Migration[7.0]
  def change
    create_table :password_reset_tokens do |t|
      t.string :email
      t.timestamps
    end
  end
end

class CreatePermissionRole < ActiveRecord::Migration[7.0]
  def change
    create_table :permission_role do |t|
      t.integer :permission_id, default: "NULL"
      t.integer :role_id, default: "NULL"
      t.timestamps
    end
  end
end

class CreatePermissions < ActiveRecord::Migration[7.0]
  def change
    create_table :permissions do |t|
      t.string :name
      t.timestamps
    end
  end
end

class CreateProductGalleries < ActiveRecord::Migration[7.0]
  def change
    create_table :product_galleries do |t|
      t.integer :product_id, null: false
      t.string :gallery
      t.timestamps
    end
  end
end

class CreateProductHistories < ActiveRecord::Migration[7.0]
  def change
    create_table :product_histories do |t|
      t.integer :id, null: false
      t.string :name
      t.timestamps
    end
  end
end

class CreateProducts < ActiveRecord::Migration[7.0]
  def change
    create_table :products do |t|
      t.string :name
      t.timestamps
    end
  end
end

class CreatePulseAggregates < ActiveRecord::Migration[7.0]
  def change
    create_table :pulse_aggregates do |t|
      t.integer :bucket, null: false
      t.string :period, null: false
      t.string :type
      t.timestamps
    end
  end
end

class CreatePulseEntries < ActiveRecord::Migration[7.0]
  def change
    create_table :pulse_entries do |t|
      t.integer :timestamp, null: false
      t.string :type
      t.timestamps
    end
  end
end

class CreatePulseValues < ActiveRecord::Migration[7.0]
  def change
    create_table :pulse_values do |t|
      t.integer :timestamp, null: false
      t.string :type
      t.timestamps
    end
  end
end

class CreateRoles < ActiveRecord::Migration[7.0]
  def change
    create_table :roles do |t|
      t.string :name
      t.timestamps
    end
  end
end

class CreateSmsTemplates < ActiveRecord::Migration[7.0]
  def change
    create_table :sms_templates do |t|
      t.string :type
      t.timestamps
    end
  end
end

class CreateTelescopeEntries < ActiveRecord::Migration[7.0]
  def change
    create_table :telescope_entries do |t|
      t.bigint :sequence, null: false
      t.string :uuid
      t.timestamps
    end
  end
end

class CreateTelescopeEntriesTags < ActiveRecord::Migration[7.0]
  def change
    create_table :telescope_entries_tags do |t|
      t.string :entry_uuid
      t.timestamps
    end
  end
end

class CreateTelescopeMonitoring < ActiveRecord::Migration[7.0]
  def change
    create_table :telescope_monitoring do |t|
      t.string :tag
      t.timestamps
    end
  end
end

class CreateUsers < ActiveRecord::Migration[7.0]
  def change
    create_table :users do |t|
      t.integer :role_id, default: "NULL"
      t.string :is_salesman
      t.timestamps
    end
  end
end