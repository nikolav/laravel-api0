import fs from "node:fs/promises";
import path from "node:path";

import juice from "juice";
import { minify } from "html-minifier-terser";

const inputPath = process.argv[2];
const outputPath = process.argv[3];

if (!inputPath || !outputPath) {
  console.error(
    "Usage: node scripts/inline-pdf-html.mjs <input.html> <output.html>",
  );
  process.exit(1);
}

const absInput = path.resolve(inputPath);
const absOutput = path.resolve(outputPath);

const inlineWithJuice = (filePath) =>
  new Promise((resolve, reject) => {
    juice.juiceFile(
      filePath,
      {
        applyStyleTags: true,
        removeStyleTags: true,
        preserveMediaQueries: true,
        preserveFontFaces: true,
        preserveImportant: true,

        // web-resource-inliner
        webResources: {
          images: true,
          svgs: true,
          links: true,
          // ignore for pdfs
          scripts: false,
          // resolve relative assets from the HTML file location
          relativeTo: path.dirname(filePath),
          // optional: make sure all images are inlined, not only tiny ones
          maxImageFileSize: Infinity,
        },
      },
      async (err, htmlInlined) => {
        try {
          if (err) throw err;
          resolve(
            await minify(htmlInlined, {
              // collapseWhitespace: true,
              html5: true,
              keepClosingSlash: true,
              minifyCSS: true,
              minifyJS: false,
              removeComments: true,
              useShortDoctype: true,
              // decodeEntities: true,
              // removeEmptyAttributes: true,
              // removeRedundantAttributes: true,
              // removeScriptTypeAttributes: true,
              // removeStyleLinkTypeAttributes: true,
              // sortAttributes: true,
              // sortClassName: true,
              // collapseBooleanAttributes: true,
              // removeAttributeQuotes: true,
              // trimCustomFragments: true,
            }),
          );
        } catch (error) {
          reject(error);
        }
      },
    );
  });

(async () => {
  try {
    const html = await inlineWithJuice(absInput);
    await fs.writeFile(absOutput, html, "utf8");
    console.log(absOutput);
  } catch (error) {
    console.error(error);
    process.exit(1);
  }
})();
