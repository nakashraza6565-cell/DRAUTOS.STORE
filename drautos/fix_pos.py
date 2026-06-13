import re

# 1. Fix pos/index.blade.php
with open('drautos/resources/views/backend/pos/index.blade.php', 'r', encoding='utf-8') as f:
    pos_content = f.read()

# Find the #complete-order block
start_idx = pos_content.find("$('#complete-order').on('click'")
if start_idx != -1:
    # Find matching closing brace
    brace_count = 0
    end_idx = -1
    for i in range(start_idx, len(pos_content)):
        if pos_content[i] == '{':
            brace_count += 1
        elif pos_content[i] == '}':
            brace_count -= 1
            if brace_count == 0:
                end_idx = i + 1
                while end_idx < len(pos_content) and pos_content[end_idx] in [')', ';', '\n', '\r', ' ']:
                    end_idx += 1
                break
    
    if end_idx != -1:
        extracted_block = pos_content[start_idx:end_idx]
        pos_content = pos_content[:start_idx] + pos_content[end_idx:]
        
        # Modify extracted block for global use
        extracted_block = extracted_block.replace("$('#complete-order').on('click', function() {", "$(document).on('click', '#complete-order', function() {")
        extracted_block = extracted_block.replace("cart: cart", "cart: window.posCart")
        
        # 2. Inject into cart_drawer.blade.php
        with open('drautos/resources/views/backend/layouts/cart_drawer.blade.php', 'r', encoding='utf-8') as f:
            drawer_content = f.read()
            
        drawer_start = drawer_content.find("$(document).on('click', '#complete-order'")
        if drawer_start != -1:
            drawer_brace_count = 0
            drawer_end = -1
            for i in range(drawer_start, len(drawer_content)):
                if drawer_content[i] == '{':
                    drawer_brace_count += 1
                elif drawer_content[i] == '}':
                    drawer_brace_count -= 1
                    if drawer_brace_count == 0:
                        drawer_end = i + 1
                        while drawer_end < len(drawer_content) and drawer_content[drawer_end] in [')', ';', '\n', '\r', ' ']:
                            drawer_end += 1
                        break
            
            if drawer_end != -1:
                drawer_content = drawer_content[:drawer_start] + extracted_block + drawer_content[drawer_end:]
                
                with open('drautos/resources/views/backend/layouts/cart_drawer.blade.php', 'w', encoding='utf-8') as f:
                    f.write(drawer_content)
                print("Updated cart_drawer.blade.php")

# Ensure @endsection is present at the very end of pos/index.blade.php
if '@endsection' not in pos_content[-50:]:
    pos_content = pos_content.rstrip() + "\n@endsection\n"

with open('drautos/resources/views/backend/pos/index.blade.php', 'w', encoding='utf-8') as f:
    f.write(pos_content)
print("Updated pos/index.blade.php")
