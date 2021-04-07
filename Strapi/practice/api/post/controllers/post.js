"use strict";

/**
 * Read the documentation (https://strapi.io/documentation/developer-docs/latest/development/backend-customization.html#core-controllers)
 * to customize this controller
 */

const { parseMultipartData, sanitizeEntity } = require("strapi-utils");

module.exports = {
  async create(ctx) {
    let entity;
    if (ctx.is("multipart")) {
      const { data, files } = parseMultipartData(ctx);

      // !data ? ctx.throw(400, "Please add description") : false;
      // !files.length ? ctx.throw(400, "Please add at least a file") : false;

      if (!data || !data.description) {
        ctx.throw(400, "Please add some content");
      }

      if (!files || !files.image) {
        ctx.throw(400, "Please add at least a file");
      }

      entity = await strapi.services.post.create(
        { ...data, likes: 0 },
        { files }
      );
    } else {
      ctx.throw(400, "You must submit a multipart request");
      // entity = await strapi.services.post.create({
      //   ...ctx.request.body,
      //   likes: 0,
      // });
    }
    return sanitizeEntity(entity, { model: strapi.models.post });
  },
};
