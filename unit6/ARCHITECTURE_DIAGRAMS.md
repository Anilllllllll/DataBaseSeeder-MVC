# Task Manager - Architecture & Flow Diagrams

## 🏗️ Application Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    WEB BROWSER                              │
│  (Views: Index, Create, Edit, Show)                        │
└────────────────────┬────────────────────────────────────────┘
                     │
         ┌───────────┴────────────┐
         │                        │
         ▼                        ▼
    ┌──────────┐          ┌────────────┐
    │HTML/CSS/ │          │Components  │
    │Tailwind  │          │Reusable    │
    └──────────┘          └────────────┘
         │                     │
         └─────────┬───────────┘
                   │
                   ▼
    ┌──────────────────────────────┐
    │  BLADE TEMPLATES             │
    │  ├─ layouts/app.blade.php    │
    │  ├─ tasks/index.blade.php    │
    │  ├─ tasks/create.blade.php   │
    │  ├─ tasks/edit.blade.php     │
    │  ├─ tasks/show.blade.php     │
    │  └─ components/              │
    │     priority-dropdown.blade  │
    └──────┬───────────────────────┘
           │
           ▼
    ┌──────────────────────────────┐
    │  ROUTER (routes/web.php)     │
    │  ├─ GET  /tasks              │
    │  ├─ POST /tasks              │
    │  ├─ PUT  /task/{id}/priority │◄─── KEY ROUTE
    │  ├─ PUT  /tasks/{id}         │
    │  ├─ DELETE /tasks/{id}       │
    │  └─ More...                  │
    └──────┬───────────────────────┘
           │
           ▼
    ┌──────────────────────────────┐
    │  CONTROLLER                  │
    │  TaskController              │
    │  ├─ index()                  │
    │  ├─ create()                 │
    │  ├─ store()                  │
    │  ├─ show()                   │
    │  ├─ edit()                   │
    │  ├─ update()                 │
    │  ├─ updatePriority()         │◄─── KEY METHOD
    │  └─ destroy()                │
    └──────┬───────────────────────┘
           │
           ▼
    ┌──────────────────────────────┐
    │  MODELS                      │
    │  ├─ Task                     │
    │  │  ├─ title                 │
    │  │  ├─ description           │
    │  │  ├─ priority              │
    │  │  └─ logs()                │
    │  │                           │
    │  └─ TaskLog                  │
    │     ├─ task_id               │
    │     ├─ old_priority          │
    │     ├─ new_priority          │
    │     └─ task()                │
    └──────┬───────────────────────┘
           │
           ▼
    ┌──────────────────────────────┐
    │  DATABASE                    │
    │  ├─ tasks table              │
    │  │  ├─ id (PK)               │
    │  │  ├─ title                 │
    │  │  ├─ description           │
    │  │  ├─ priority              │
    │  │  └─ timestamps            │
    │  │                           │
    │  └─ task_logs table          │
    │     ├─ id (PK)               │
    │     ├─ task_id (FK)          │
    │     ├─ old_priority          │
    │     ├─ new_priority          │
    │     └─ timestamps            │
    └──────────────────────────────┘
```

---

## 📊 Priority Update Flow (PUT /task/{task}/priority)

```
USER ACTION
    │
    ▼
┌─────────────────────────────────────┐
│ Select Priority from Dropdown       │
│ Click "Update Priority" Button      │
└──────────────┬──────────────────────┘
               │
               ▼
        ┌──────────────────┐
        │ HTTP PUT Request │
        │ /task/1/priority │
        │ priority: 4      │
        └──────────┬───────┘
                   │
                   ▼
        ┌──────────────────────────────┐
        │ Laravel Router               │
        │ Match Route & Bind Task Model│
        │ Resolve to Task ID: 1        │
        └──────────┬───────────────────┘
                   │
                   ▼
        ┌─────────────────────────────────────────┐
        │ TaskController@updatePriority()         │
        │                                         │
        │ 1. Validate input:                      │
        │    priority must be 1-5, integer       │
        └──────────┬────────────────────────────┘
                   │
         ┌─────────┴────────────┐
         │ Valid?               │ Invalid?
         ▼                      ▼
    Continue            Return with errors
         │              Redirect back with
         │              error messages
         │
         ▼
    ┌────────────────────────────┐
    │ 2. Check if priority       │
    │    actually changed        │
    └──────────┬─────────────────┘
               │
       ┌───────┴────────┐
       │ Changed?       │ Same?
       ▼                ▼
    Yes             Skip logging
       │
       ▼
    ┌──────────────────────────────────────┐
    │ 3. Update Task Model                 │
    │    $task->update([                   │
    │        'priority' => $newPriority    │
    │    ])                                │
    └──────────┬───────────────────────────┘
               │
               ▼
    ┌──────────────────────────────────────┐
    │ 4. Create TaskLog Record             │
    │    TaskLog::create([                 │
    │        'task_id' => $task->id,       │
    │        'old_priority' => $old,       │
    │        'new_priority' => $new,       │
    │    ])                                │
    └──────────┬───────────────────────────┘
               │
               ▼
    ┌──────────────────────────────────────┐
    │ 5. Database Transaction              │
    │    - Update tasks table              │
    │    - Insert into task_logs table     │
    └──────────┬───────────────────────────┘
               │
               ▼
    ┌──────────────────────────────────────┐
    │ 6. Return Response                   │
    │    - Redirect to previous page       │
    │    - Session flash: success message  │
    │    - HTTP 302 Redirect               │
    └──────────┬───────────────────────────┘
               │
               ▼
    ┌──────────────────────────────────────┐
    │ User sees:                           │
    │ ✅ "Priority updated successfully"  │
    │ Updated task with new priority      │
    │ Change logged in history             │
    └──────────────────────────────────────┘
```

---

## 🔄 Data Flow: Creating a Task

```
┌─────────────────────────┐
│ User: /tasks/create     │
└──────────┬──────────────┘
           │
           ▼
    ┌─────────────────────────────────────────┐
    │ Show Create Form                        │
    │ - Title input                           │
    │ - Description textarea                  │
    │ - Priority dropdown (x-priority-dropdown)│
    │ - Submit button                         │
    └──────────┬────────────────────────────┘
               │ POST /tasks
               ▼
    ┌─────────────────────────────────────────┐
    │ TaskController::store()                 │
    │                                         │
    │ 1. Validate:                            │
    │    - title: required, string, max:255   │
    │    - description: nullable, string      │
    │    - priority: required, 1-5            │
    └──────────┬────────────────────────────┘
               │
    ┌──────────┴──────────┐
    │ Valid?              │ Invalid?
    ▼                     ▼
Continue      Redirect back with
    │         error bag
    │
    ▼
┌─────────────────────────────────┐
│ 2. Create Task Record           │
│    Task::create($validated)     │
└──────────┬──────────────────────┘
           │
           ▼
    ┌─────────────────────────────────────┐
    │ 3. Database Insert                  │
    │    INSERT INTO tasks (              │
    │        title, description, priority │
    │    ) VALUES (...)                   │
    │    Priority defaults to 3 if needed │
    └──────────┬──────────────────────────┘
               │
               ▼
    ┌─────────────────────────────────────┐
    │ 4. Redirect Response                │
    │    - Redirect to tasks.index        │
    │    - Session: success message       │
    │    - HTTP 302                       │
    └──────────┬──────────────────────────┘
               │
               ▼
    ┌─────────────────────────────────────┐
    │ User sees:                          │
    │ ✅ Task created successfully!       │
    │ Task appears in list                │
    │ With selected priority              │
    └─────────────────────────────────────┘
```

---

## 📈 Priority Change History View

```
Task Detail Page (/tasks/{id})
┌─────────────────────────────────────┐
│ Task Title                          │
│ ID: 1                               │
│ Description: ...                    │
│ Priority: 4 (Green dropdown)        │
├─────────────────────────────────────┤
│ Priority Change History             │
├─────────────────────────────────────┤
│                                     │
│ Query: $task->logs()                │
│   .orderBy('created_at', 'desc')    │
│                                     │
│ Table:                              │
│ ┌──────────────────────────────────┐│
│ │ From  │ To    │ Date            ││
│ ├──────────────────────────────────┤│
│ │  1    │  2    │ 2026-06-09 05:30││
│ │  🔴   │  🟠   │                 ││
│ ├──────────────────────────────────┤│
│ │  2    │  1    │ 2026-06-09 05:35││
│ │  🟠   │  🔴   │                 ││
│ ├──────────────────────────────────┤│
│ │  1    │  4    │ 2026-06-09 06:00││
│ │  🔴   │  🟢   │                 ││
│ └──────────────────────────────────┘│
└─────────────────────────────────────┘

Each log shows:
- old_priority (displayed with dropdown component)
- new_priority (displayed with dropdown component)
- created_at timestamp
- All from task_logs table
```

---

## 🗄️ Database Relationship Diagram

```
tasks
├─────────────────────────────────────────┐
│ PK: id                                  │
│ title          VARCHAR(255) NOT NULL    │
│ description    LONGTEXT NULLABLE        │
│ priority       INT DEFAULT 3            │
│ created_at     TIMESTAMP                │
│ updated_at     TIMESTAMP                │
└──────────────────┬──────────────────────┘
                   │ (1)
                   │
            ┌──────┴────────────────────┐
            │ hasMany('logs')           │
            │ Foreign Key: task_id      │
            │                           │
                   (Many)
                   │
┌──────────────────┘
│
└─ task_logs
   ├────────────────────────────────────────┐
   │ PK: id                                 │
   │ FK: task_id (references tasks.id)      │
   │     ON DELETE CASCADE                  │
   │ old_priority   INT NOT NULL            │
   │ new_priority   INT NOT NULL            │
   │ created_at     TIMESTAMP               │
   │ updated_at     TIMESTAMP               │
   └────────────────────────────────────────┘

Relationship:
Task (1) ──hasMany──> TaskLog (Many)
TaskLog (Many) ──belongsTo──> Task (1)
```

---

## 🎨 Blade Component Flow

```
<x-priority-dropdown
    :priority="3"
    :disabled="false"
/>

                    │
                    ▼
        ┌────────────────────────────┐
        │ Component Props            │
        │ - priority: 3 (current)    │
        │ - disabled: false          │
        │ - name: 'priority' (default)
        └────────────┬───────────────┘
                     │
                     ▼
        ┌────────────────────────────────────────┐
        │ Determine Styling Based on Priority   │
        │                                        │
        │ priority == 1 → Red (#dc3545)         │
        │ priority == 2 → Orange (#fd7e14)      │
        │ priority == 3 → Yellow (#ffc107)      │
        │ priority == 4 → Green (#28a745)       │
        │ priority == 5 → Gray (#6c757d)        │
        └────────────┬─────────────────────────┘
                     │
                     ▼
        ┌────────────────────────────────────────┐
        │ Render HTML <select>                   │
        │ - Background color based on priority  │
        │ - Options: 1, 2, 3, 4, 5              │
        │ - Current value marked with @selected │
        │ - Disabled attribute if :disabled=true│
        │ - Added hover/focus effects           │
        │ - Added CSS classes for styling       │
        └────────────┬─────────────────────────┘
                     │
                     ▼
        ┌────────────────────────────────────────┐
        │ Output:                                │
        │ <select class="priority-dropdown"      │
        │         style="background: yellow...">│
        │   <option value="1" 🔴 Critical>      │
        │   <option value="2" 🟠 High>          │
        │   <option value="3" 🟡 Medium selected>
        │   <option value="4" 🟢 Low>           │
        │   <option value="5" ⚫ Minimal>       │
        │ </select>                              │
        └────────────────────────────────────────┘
```

---

## 🔐 Validation Pipeline

```
User Input
    │
    ▼
┌─────────────────────────────────────────┐
│ HTTP Request                            │
│ POST /tasks or PUT /task/{id}/priority  │
└──────────┬──────────────────────────────┘
           │
           ▼
    ┌─────────────────────────────────────┐
    │ Laravel Validator                   │
    │                                     │
    │ if (updatePriority):                │
    │   'priority' => 'required|          │
    │                 integer|            │
    │                 between:1,5'        │
    │                                     │
    │ if (create/update):                 │
    │   'title' => 'required|string|      │
    │              max:255'               │
    │   'description' => 'nullable|       │
    │                   string'           │
    │   'priority' => 'required|          │
    │                 integer|            │
    │                 between:1,5'        │
    └──────────┬──────────────────────────┘
               │
       ┌───────┴──────────┐
       │ Passes?          │ Fails?
       ▼                  ▼
    Continue      ┌─────────────────────┐
       │          │ Return with Errors  │
       │          │ Redirect back       │
       │          │ Flash error messages│
       │          │ Re-populate form    │
       │          └─────────────────────┘
       │
       ▼
    ┌──────────────────────────────────┐
    │ Process Validated Data           │
    │ Create/Update records            │
    │ Log changes if applicable        │
    └──────────────────────────────────┘
       │
       ▼
    ┌──────────────────────────────────┐
    │ Return Success Response          │
    │ Redirect with success message    │
    └──────────────────────────────────┘
```

---

## 📱 Task List Page Structure

```
┌─────────────────────────────────────────────────────────────┐
│ Task Manager (Navigation Bar)                              │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ [Create Task Button]                                        │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐│
│ │ ID │ Title      │ Description │ Priority │ Update│Action││
│ ├──────────────────────────────────────────────────────────┤│
│ │ 1  │ Bug fix    │ Auth issue  │ 🔴 [2] 🔴│[Update]│V E D││
│ ├──────────────────────────────────────────────────────────┤│
│ │ 2  │ New feature│ Dashboard   │ 🟡 [3]   │[Update]│V E D││
│ ├──────────────────────────────────────────────────────────┤│
│ │ 3  │ Deploy app│ Production  │ 🟠 [2]   │[Update]│V E D││
│ ├──────────────────────────────────────────────────────────┤│
│ │ 4  │ Docs      │ API docs    │ 🟢 [4]   │[Update]│V E D││
│ └──────────────────────────────────────────────────────────┘│
│                                                             │
│ Legend: V=View, E=Edit, D=Delete                           │
│         [#] = Priority Dropdown                            │
│         [Update] = Update Button                           │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 Test Coverage Map

```
TaskPriorityUpdateTest
│
├─ test_priority_can_be_updated
│  └─ Verify task priority updates
│
├─ test_priority_change_is_logged
│  └─ Verify TaskLog record created
│
├─ test_no_log_created_when_priority_unchanged
│  └─ Verify no log if priority same
│
├─ test_priority_validation_lower_bound
│  └─ Reject priority < 1
│
├─ test_priority_validation_upper_bound
│  └─ Reject priority > 5
│
├─ test_priority_is_required
│  └─ Reject missing priority
│
├─ test_all_valid_priority_levels
│  └─ Verify 1,2,3,4,5 all work
│
├─ test_multiple_priority_changes_are_logged
│  └─ Verify multiple logs tracked
│
├─ test_task_log_foreign_key_constraint
│  └─ Verify FK relationship works
│
├─ test_priority_must_be_integer
│  └─ Reject non-integer values
│
└─ test_success_message_returned
   └─ Verify user feedback message
```

---

## 🎯 Request Routing Map

```
REQUEST TYPE │ ROUTE            │ METHOD           │ VIEW/ACTION
─────────────┼──────────────────┼──────────────────┼───────────────────
GET          │ /tasks           │ index()          │ List all tasks
GET          │ /tasks/create    │ create()         │ Create form
POST         │ /tasks           │ store()          │ Save & redirect
GET          │ /tasks/1         │ show()           │ Task detail
GET          │ /tasks/1/edit    │ edit()           │ Edit form
PUT          │ /tasks/1         │ update()         │ Update & redirect
PUT          │ /task/1/priority │ updatePriority() │ Update priority ⭐
DELETE       │ /tasks/1         │ destroy()        │ Delete & redirect
```

---

## 💾 Data Flow in updatePriority()

```
Request Data: {priority: 4}
    │
    ▼
Validation
    ├─ required: Yes ✓
    ├─ integer: Yes ✓
    └─ between:1,5: Yes ✓
    │
    ▼
Get Current Priority: 2
    │
    ▼
Compare: 2 !== 4 ?
    │       └─ Yes, continue
    ▼
Update Task Model
    └─ priority: 2 → 4
    │
    ▼
Create Log Entry
    ├─ task_id: 1
    ├─ old_priority: 2
    ├─ new_priority: 4
    └─ created_at: now()
    │
    ▼
Database Commit
    ├─ UPDATE tasks SET priority=4 WHERE id=1
    └─ INSERT INTO task_logs (...) VALUES (...)
    │
    ▼
Return Response
    ├─ Status: 302 (Redirect)
    ├─ Location: Previous page
    └─ Session: {success: "Priority updated successfully"}
    │
    ▼
User Sees
    ├─ Success message flash
    ├─ Task with new priority (4 - Green)
    └─ Change in history log
```

---

This architecture ensures:

- ✅ Clean separation of concerns
- ✅ Proper data validation
- ✅ Automatic logging of changes
- ✅ Reusable components
- ✅ Complete test coverage
- ✅ User-friendly interface
