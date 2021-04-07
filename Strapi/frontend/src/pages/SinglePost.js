import { useState, useEffect } from "react";
import Post from "../components/Post";

const SinglePost = ({ match }) => {
  const url = "http://localhost:1337";
  const { id } = match.params;
  const [post, setPost] = useState({});

  useEffect(() => {
    const fetchPost = async () => {
      const response = await fetch(`${url}/posts/${id}`);
      const data = await response.json();
      setPost(data);
      console.log(data);
    };
    fetchPost();
  });

  return (
    <div>
      <Post
        description={post.description}
        url={post.image && post.image.url}
        likes={post.likes}
      />
    </div>
  );
};

export default SinglePost;
