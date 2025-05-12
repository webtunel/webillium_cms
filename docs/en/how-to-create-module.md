# How To Create Module (CRUD)
Go to **Module Generator** menu -> Add New Module

## Module Wizard
The Module Wizard is available for both new module creation and editing existing modules. You can access the wizard by:
- Creating a new module from the "Generate New Module" button
- Clicking the "Module Wizard" action button on any existing module

## Step 1
| Field Name      | Description         |
| ----------------|---------------------|
| Table Name      | Module table name   |
| Icon            | Module icon         |
| Module Slug     | Module slug for url |
| Button Action Style | Style of action buttons (button_icon, button_icon_text, button_text, dropdown) |

```
(v) also create module for this menu
```
You might check if you want create the menu also

Additional settings:
- **Button Action Style**: Choose between `button_icon`, `button_icon_text`, `button_text`, or `dropdown`
- **Button Settings**: Each action button (add, edit, delete, detail, show, filter, export, import) can be enabled or disabled
- **Global Privilege**: Enable to make the module accessible by all privileges

## Step 2
| Field Name      | Description         |
| ----------------|---------------------|
| Column | The table column name |
| Name | The field name according to the current table |
| Callback Php | You might add some php code |
| Width | Column width |
| Image | If this column is image, you might choose Y |
| Download | IF this column is downloadable, you might choose Y |

## Step 3
| Field Name      | Description         |
| ----------------|---------------------|
| Label | Label of row |
| Name | The field name according to the current table |
| Validation | Laravel validation |
| Width | Column width |
| Options | You need set additional option by click this button |

## Step 4
| Field Name      | Description         |
| ----------------|---------------------|
| Field Candidate | The field name of title candidate |
| Limit Data | Amount to limit |
| Order By | e.g : `fieldName,asc`, `fieldName,desc` |
| Show Button Table Action | Check if you want to show action button | 
| Show Button Add | Check if you want to show add button |
| Show Button Edit | Check if you want to show edit button |
| Show Button Delete | Check if you want to show delete button |
| Show Button Detail | Check if you want to show detail button |
| Show Button ShowData | Check if you want to show showdata button |
| Show Filtering | Check if you want to show filtering button |
| Show Import | Check if you want to show import button | 
| Show Export | Check if you want to show export button |

### Default Values
By default, button settings are configured as follows:
- All buttons (add, edit, delete, detail, show, filtering) default to 'Yes'
- Export and import buttons default to 'No'

## Database Support
Webillium CMS supports multiple database types:
- MySQL/MariaDB
- PostgreSQL (including schema support)
- Other Laravel-supported databases

## Maintenance Features
The system includes maintenance features to help manage your modules:
- Button settings can be fixed automatically for all modules
- Module controllers are generated with proper namespace and imports
- Support for custom debugging through logs

## What's Next
- [How To Add Column In Grid Data](./how-to-add-column.md)

## Table Of Contents
- [Back To Index](./index.md)