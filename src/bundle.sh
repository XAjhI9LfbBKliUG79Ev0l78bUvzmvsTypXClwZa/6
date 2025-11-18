#!/bin/bash

# Define the output file
output_file="combined.txt"

# Clear the output file or create it
> "$output_file"

# Search for all .php files and process each one
find "$(pwd)" -name "*.php" | while read -r file; do
    echo "File: $file" >> "$output_file"   # Write the header with file path
    cat "$file" >> "$output_file"          # Append file contents
    echo -e "\n" >> "$output_file"          # Add a newline for separation
done

echo "All PHP files have been combined into $output_file."
