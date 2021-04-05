/* eslint-disable jsx-a11y/alt-text */
/* eslint-disable no-unused-vars */
/* eslint-disable import/no-anonymous-default-export */
import React from "react";

// mock-data
const post = {
  id: 1,
  description: "Just a random image",
  likes: 10,
  author: null,
  published_at: "2021-04-05T18:48:46.599Z",
  created_at: "2021-04-05T18:46:31.431Z",
  updated_at: "2021-04-05T18:48:46.624Z",
  image: {
    id: 1,
    name: "Hero_Mobile.png",
    alternativeText: "",
    caption: "",
    width: 376,
    height: 376,
    formats: {
      thumbnail: {
        name: "thumbnail_Hero_Mobile.png",
        hash: "thumbnail_Hero_Mobile_10a3fd150c",
        ext: ".png",
        mime: "image/png",
        width: 156,
        height: 156,
        size: 27.12,
        path: null,
        url: "/uploads/thumbnail_Hero_Mobile_10a3fd150c.png",
      },
    },
    hash: "Hero_Mobile_10a3fd150c",
    ext: ".png",
    mime: "image/png",
    size: 94.24,
    url: "/uploads/Hero_Mobile_10a3fd150c.png",
    previewUrl: null,
    provider: "local",
    provider_metadata: null,
    created_at: "2021-04-05T18:46:01.093Z",
    updated_at: "2021-04-05T18:46:01.106Z",
  },
};

const API_URL = "http://localhost:1337";

const formatImageUrl = (url) => `${API_URL}${url}`;

export default () => {
  const url = post.image && post.image.url;
  const description = post.description;
  const likes = post.likes;

  console.log(url);
  console.log(description);
  console.log(likes);

  return (
    <div className="Post">
      <img src={formatImageUrl(url)}></img>
      <h4>{description}</h4>
      <div>
        <span>Likes: {likes}</span>
      </div>
    </div>
  );
};
