# Button Action Styles and Maintenance

Webillium CMS offers various ways to customize how action buttons appear and behave in your modules. This guide explains button action styles and button maintenance features.

## Button Action Styles

When creating or editing a module, you can choose from four different styles for your action buttons:

| Style Option | Description |
|--------------|-------------|
| `button_icon` | Shows only the icon (most compact) |
| `button_icon_text` | Shows both icon and text label |
| `button_text` | Shows only the text label without icons |
| `dropdown` | Places all actions in a dropdown menu |

### Setting Button Action Style

You can set the button action style in two ways:

1. **During module creation** in Step 1 of the Module Wizard
2. **In your controller** by adding this to your `cbInit()` method:

```php
public function cbInit() 
{
    // Other configuration...
    $this->button_action_style = 'button_icon_text';
    // Rest of your configuration...
}
```

### Examples of Each Style

#### button_icon (Default)
Shows only icons, saving space in your grid:
```php
$this->button_action_style = 'button_icon';
```

#### button_icon_text
Shows both icons and text for better clarity:
```php
$this->button_action_style = 'button_icon_text';
```

#### button_text
Shows only text labels without icons:
```php
$this->button_action_style = 'button_text';
```

#### dropdown
Places all actions in a dropdown menu to save space:
```php
$this->button_action_style = 'dropdown';
```

## Button Visibility Control

For each module, you can control the visibility of various action buttons:

| Button Option | Code Setting | Default |
|---------------|-------------|---------|
| Table Action | `$this->button_table_action` | Yes |
| Add | `$this->button_add` | Yes |
| Delete | `$this->button_delete` | Yes |
| Edit | `$this->button_edit` | Yes |
| Detail | `$this->button_detail` | Yes |
| Show Data | `$this->button_show` | Yes |
| Filter | `$this->button_filter` | Yes |
| Export | `$this->button_export` | No |
| Import | `$this->button_import` | No |
| Bulk Actions | `$this->button_bulk_action` | Yes |

Example of disabling several buttons:

```php
public function cbInit() 
{
    // Other configuration...
    $this->button_export = false; // or 'No'
    $this->button_import = false; // or 'No'
    $this->button_detail = false; // or 'No'
    // Rest of your configuration...
}
```

## Maintenance Features

Webillium CMS includes maintenance features to help you manage button settings across all modules.

### Automatic Button Fix

If you find that button settings in your controllers don't match what's shown in the grid, you can use the built-in button fix utility:

1. **Access the route**: `/admin/modules/fix-buttons` (requires Superadmin access)
2. This will analyze all module controllers and fix button settings automatically

### Programmatic Button Fix

You can also fix button settings programmatically for a specific controller:

```php
// In a command or custom controller
use webtunel\webilliumcms\controllers\ModulsController;

$modulController = new ModulsController();
$fixed = $modulController->fixModuleControllerButtons('YourControllerName');
```

## Default Button Values

When creating a new module, these are the default button settings:

- **Enabled by default** (`Yes`):
  - Add button
  - Edit button
  - Delete button
  - Detail button
  - Show Data button
  - Filter button
  - Table Actions
  
- **Disabled by default** (`No`):
  - Export button
  - Import button

These default values help create a clean and efficient UI while ensuring essential functionality remains accessible.

## What's Next
- [How To Add More Action Button In Grid Data](./how-add-more-action-button.md)
- [How To Add More Button At Top Of Grid Data](./how-add-button-top-grid-data.md)

## Table Of Contents
- [Back To Index](./index.md)