#!/bin/bash

# This script demonstrates how to use our custom artisan commands to generate
# a complete resource with repository, service, and form requests

# Create model and migration
echo "Creating Post model and migration..."
php artisan make:model Post -m

# Generate repository
echo "Generating Post repository..."
php artisan make:repository Post --model=Post

# Generate service
echo "Generating Post service..."
php artisan make:service Post --repository=Post --register

# Generate form requests
echo "Generating Post form requests..."
php artisan make:form-request Post --model=Post

echo "Resources generated successfully!"
echo "Remember to run migrations and update the model's fillable attributes."

