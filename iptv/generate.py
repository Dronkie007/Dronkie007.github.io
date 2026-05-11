import os

input_file = 'everything.m3u'
output_folder = 'everything'
output_html = 'index.html'

# Ensure the output folder exists
os.makedirs(output_folder, exist_ok=True)

html_links = []

with open(input_file, 'r', encoding='utf-8') as file:
    lines = file.readlines()

i = 0
while i < len(lines):
    if lines[i].startswith('#EXTINF'):
        name_line = lines[i].strip()
        url_line = lines[i+2].strip() if lines[i+1].startswith('#EXTVLCOPT') else lines[i+1].strip()

        # Extract channel name
        name = name_line.split(',')[-1].strip().replace(' ', '_').replace('/', '_')

        # Create individual M3U file
        m3u_content = f"{name_line}\n#EXTVLCOPT:network-caching=1000\n{url_line}\n"
        filename = f"{name}.m3u"
        with open(os.path.join(output_folder, filename), 'w', encoding='utf-8') as out_file:
            out_file.write(m3u_content)

        # Add HTML link
        html_links.append(f"<li><a href='{output_folder}/{filename}'>{name.replace('_', ' ')}</a></li>")

        i += 3 if lines[i+1].startswith('#EXTVLCOPT') else 2
    else:
        i += 1

# Write index.html
with open(output_html, 'w', encoding='utf-8') as html:
    html.write("<!DOCTYPE html>\n<html>\n<head>\n<title>Channel List</title>\n</head>\n<body>\n")
    html.write("<h1>All Channels</h1>\n<ul>\n")
    html.write("\n".join(html_links))
    html.write("\n</ul>\n</body>\n</html>")
