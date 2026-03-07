import fs from "node:fs/promises";
import path from "node:path";

import juice from "juice";
import mri from "mri";

const args = mri(process.argv.slice(2));

const inputPath = args["input"];
const outputPath = args["output"];
const relativeTo = args["relative-to"];

if (!inputPath || !outputPath) {
  console.error(
    "Usage: [$ node scripts/inline-pdf-html.mjs --input <input.html> --output <output.html> --relative-to <resolve.html>]",
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
      (error, htmlInlined) => {
        if (error) {
          reject(error);
          return;
        }
        resolve(htmlInlined);
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
