const sharp = require('sharp');
const fs = require('fs');

let svg = fs.readFileSync('dr_svg.html', 'utf8');
svg = svg.replace('width="154"', 'width="1540"').replace('height="99.9"', 'height="999"');

sharp(Buffer.from(svg))
  .png()
  .toBuffer()
  .then(data => {
    fs.writeFileSync('dr_base64.txt', data.toString('base64'));
    console.log('Done!');
  })
  .catch(err => console.error(err));
