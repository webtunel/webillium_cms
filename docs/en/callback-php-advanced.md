# Advanced Usage of callback_php

The `callback_php` feature allows you to process and transform data in your grid columns using custom PHP code. This powerful feature enables complex data manipulation and formatting with simple inline PHP expressions.

## Callback PHP vs Regular Callback

Webillium CMS offers two ways to transform column data:

1. **callback_php**: A string containing PHP code that will be evaluated
   ```php
   $this->col[] = ["label"=>"Price","name"=>"price","callback_php"=>'number_format($row->price)'];
   ```

2. **callback**: A full PHP function with access to the entire row object
   ```php
   $this->col[] = ["label"=>"Price","name"=>"price","callback"=>function($row) {
       return number_format($row->price);
   }];
   ```

## Best Practices for callback_php

1. **Always use single quotes** for the PHP code string to avoid variable interpolation issues:
   ```php
   // CORRECT
   "callback_php"=>'number_format($row->price)'
   
   // INCORRECT - may cause issues
   "callback_php"=>"number_format($row->price)"
   ```

2. **Access row data** using either of these methods:
   ```php
   // Using $row object (preferred for complex data)
   "callback_php"=>'number_format($row->price)'
   
   // Using column name in brackets (simpler syntax)
   "callback_php"=>'number_format([price])'
   ```

3. **Conditional formatting** is fully supported:
   ```php
   "callback_php"=>'$row->status=="active" ? "<span class=\"label label-success\">Active</span>" : "<span class=\"label label-default\">Inactive</span>"'
   ```

4. **Combine multiple functions**:
   ```php
   "callback_php"=>'strtoupper(substr($row->name, 0, 10))."..."'
   ```

5. **Format dates**:
   ```php
   "callback_php"=>'date("d M Y", strtotime($row->created_at))'
   ```

## Advanced Examples

### Currency Formatting
```php
"callback_php"=>'($row->price > 1000) ? "<strong>$".number_format($row->price,2)."</strong>" : "$".number_format($row->price,2)'
```

### Status Indicators with Icons
```php
"callback_php"=>'($row->status=="paid") ? "<i class=\"fa fa-check text-success\"></i> Paid" : "<i class=\"fa fa-times text-danger\"></i> Unpaid"'
```

### Progress Bar for Percentage
```php
"callback_php"=>'
"<div class=\"progress\">
  <div class=\"progress-bar\" role=\"progressbar\" style=\"width: ".$row->completion_percent."%;\">
    ".$row->completion_percent."%
  </div>
</div>"
'
```

### Text Truncation with Tooltip
```php
"callback_php"=>'
"<span data-toggle=\"tooltip\" title=\"".$row->full_description."\">".
substr($row->full_description, 0, 50).
(strlen($row->full_description) > 50 ? "..." : "").
"</span>"
'
```

## Debugging callback_php

If your callback isn't working as expected:

1. Start with a simple version of your code to ensure it works
2. Add complexity step by step
3. Test with hard-coded values first
4. Use `var_dump()` wrapped in `<pre>` tags for debugging (but remove for production)

```php
"callback_php"=>'
"<pre>".var_dump($row)."</pre>"
'
```

## Security Considerations

Remember that code in `callback_php` is evaluated, so:

1. Never use user-supplied data in your callback_php expressions
2. Escape any HTML output to prevent XSS attacks
3. Keep expressions focused on display formatting, not business logic

## What's Next
- [How To Put Number Format In Grid Data Column (callback)](./how-to-put-number-format-callback.md)
- [How To Make A Subquery Column in Grid Data](./how-to-make-subquery.md)

## Table Of Contents
- [Back To Index](./index.md)