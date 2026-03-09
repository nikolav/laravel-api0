import fs from "node:fs/promises";
import path from "node:path";

import juice from "juice";
import mri from "mri";
import { minify } from "html-minifier-terser";

const args = mri(process.argv.slice(2));

const inputPath = args["input"];
const outputPath = args["output"];
const relativeTo = args["assets-relative-to"];

if (!inputPath || !outputPath) {
  console.error(
    `
    # Usage:
    $ node scripts/inline-pdf-html.mjs \
      --input <input.html> \
      --output <output.html> \
      --assets-relative-to <resolve.html>
    `,
  );
  process.exit(1);
}

const absInput = path.resolve(inputPath);
const absOutput = path.resolve(outputPath);
const absRelativeTo = relativeTo ? path.resolve(relativeTo) : null;

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
          relativeTo: path.dirname(absRelativeTo || filePath),

          // optional: make sure all images are inlined, not only tiny ones
          maxImageFileSize: Infinity,
        },
      },
      async (error, htmlInlined) => {
        try {
          if (error) throw error;
          resolve(
            await minify(htmlInlined, {
              minifyJS: false,
              minifyCSS: true,
              collapseWhitespace: true,
              html5: true,
              keepClosingSlash: true,
              removeComments: true,
              useShortDoctype: true,
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
    await fs.writeFile(absOutput, await inlineWithJuice(absInput), "utf8");
    console.log(absOutput);
  } catch (error) {
    console.error(error);
    process.exit(1);
  }
})();
