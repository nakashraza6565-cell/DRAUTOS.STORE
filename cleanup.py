import re
import sys

with open('drautos/resources/views/backend/pos/index.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace let cart = []; with nothing (we use window.posCart)
content = content.replace('let cart = [];', '')

# Replace cart.push, cart.forEach, cart.length etc.
# We will just replace 'cart.' with 'window.posCart.'
content = re.sub(r'\bcart\.', 'window.posCart.', content)
content = re.sub(r'\bcart\[', 'window.posCart[', content)

# Remove the duplicated functions from pos/index.blade.php
def remove_function(func_name, code):
    # Match function func_name(...) { ... }
    pattern = r'function\s+' + func_name + r'\s*\([^)]*\)\s*\{'
    match = re.search(pattern, code)
    if not match:
        return code
    
    start_idx = match.start()
    brace_count = 0
    in_string = False
    string_char = ''
    i = start_idx
    while i < len(code):
        char = code[i]
        
        # Handle strings to not get confused by braces inside strings
        if char in ['"', "'", '`']:
            if not in_string:
                in_string = True
                string_char = char
            elif string_char == char and code[i-1] != '\\':
                in_string = False
        
        if not in_string:
            if char == '{':
                brace_count += 1
            elif char == '}':
                brace_count -= 1
                if brace_count == 0:
                    end_idx = i + 1
                    return code[:start_idx] + code[end_idx:]
        i += 1
    return code

# Also remove $(document).on('click', '#complete-order' ... ) block
def remove_jq_on(selector, code):
    pattern = r"\$\(document\)\.on\([^,]+,\s*['\"]" + selector + r"['\"]\s*,\s*function\s*\([^)]*\)\s*\{"
    match = re.search(pattern, code)
    if not match:
        return code
    
    start_idx = match.start()
    brace_count = 0
    in_string = False
    string_char = ''
    i = start_idx
    while i < len(code):
        char = code[i]
        if char in ['"', "'", '`']:
            if not in_string:
                in_string = True
                string_char = char
            elif string_char == char and code[i-1] != '\\':
                in_string = False
        
        if not in_string:
            if char == '{':
                brace_count += 1
            elif char == '}':
                brace_count -= 1
                if brace_count == 0:
                    # Need to also skip the closing `);`
                    end_idx = i + 1
                    while end_idx < len(code) and code[end_idx] in [' ', '\n', '\r', '\t', ')', ';']:
                        end_idx += 1
                    return code[:start_idx] + code[end_idx:]
        i += 1
    return code

content = remove_function('renderCart', content)
content = remove_function('updateSummary', content)
content = remove_function('updatePrice', content)
content = remove_function('updateQty', content)
content = remove_function('removeFromCart', content)
content = remove_jq_on('#complete-order', content)
content = remove_jq_on('#clear-cart', content)

# Change renderCart(); to window.saveCart(); inside addToCart
content = content.replace('renderCart();', 'window.saveCart();')

with open('drautos/resources/views/backend/pos/index.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Cleanup complete.")
