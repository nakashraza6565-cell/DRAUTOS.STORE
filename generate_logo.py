import os
try:
    from PIL import Image, ImageDraw, ImageFont
except ImportError:
    import subprocess
    subprocess.check_call(['pip', 'install', 'Pillow'])
    from PIL import Image, ImageDraw, ImageFont

# Create a transparent image
width, height = 120, 100
img = Image.new('RGBA', (width, height), (255, 255, 255, 0))
draw = ImageDraw.Draw(img)

# Load the Revue font
font_path = r'C:\Users\T L S\proj\drautos.store\frontend\fonts\Revue.ttf'
font_d = ImageFont.truetype(font_path, 85)
font_r = ImageFont.truetype(font_path, 75)

# Colors
navy = (8, 50, 89, 255)      # var(--primary) #083259
silver = (163, 177, 198, 255) # #a3b1c6
white = (255, 255, 255, 255)

# Draw D (Navy)
draw.text((0, 0), 'D', font=font_d, fill=navy)

# Draw R (Silver) with a white outline for the cutout effect
r_x, r_y = 35, 15

# Outline (white shadow)
outline_width = 3
for dx in range(-outline_width, outline_width+1):
    for dy in range(-outline_width, outline_width+1):
        if dx*dx + dy*dy <= outline_width*outline_width:
            draw.text((r_x + dx, r_y + dy), 'R', font=font_r, fill=white)

# Inner text (Silver)
draw.text((r_x, r_y), 'R', font=font_r, fill=silver)

# Save to the frontend images directory
output_path = r'C:\Users\T L S\proj\drautos.store\frontend\img\dr_logo_revue.png'
img.save(output_path)
print('Logo generated successfully at:', output_path)
