#!/bin/bash

# Script to update namespaces in PHP files
# Change from crocodicstudio\crudbooster to webtunel\webilliumcms

find ./src -type f -name "*.php" -exec sed -i '' 's/crocodicstudio\\crudbooster/webtunel\\webilliumcms/g' {} \;
find ./src -type f -name "*.blade.php" -exec sed -i '' 's/crocodicstudio\\crudbooster/webtunel\\webilliumcms/g' {} \;

echo "Namespace update completed successfully!"