## Issue Fix Summary

### Problem
SQLSTATE[23000]: Integrity constraint violation: 1052 Column 'id' in field list is ambiguous

### Root Cause
In UserManagementController@edit, line 334:
```php
'area_manager_ids' => $user->assignedAreaManagers()->pluck('id')->toArray(),
```

The SQL query was:
```sql
select `id` from `users` inner join `user_area_managers` on `users`.`id` = `user_area_managers`.`area_manager_id` where `user_area_managers`.`user_id` = 5
```

Both `users` and `user_area_managers` tables have an `id` column, making the column reference ambiguous.

### Solution
Changed the problematic line to explicitly specify the table:
```php
'area_manager_ids' => $user->assignedAreaManagers()->pluck('users.id')->toArray(),
```

This generates the correct SQL:
```sql
select `users`.`id` from `users` inner join `user_area_managers` on `users`.`id` = `user_area_managers`.`area_manager_id` where `user_area_managers`.`user_id` = 5
```

### Status
✅ **FIXED** - The /qms/users/{id}/edit route should now work without SQL errors.

### Testing
- Created test scripts to verify the relationship works correctly
- Confirmed UserManagementController@edit logic executes without errors
- Area manager assignment feature is now functional