/* eslint-disable react-hooks/exhaustive-deps */
import { useState, useEffect } from "react";
import Post from "../components/Post";

const SinglePost = ({ match }) => {
  const url = "http://localhost:1337";
  const { id } = match.params;
  const [post, setPost] = useState({});
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchPost = async () => {
      const response = await fetch(`${url}/posts/${id}`);
      const data = await response.json();
      setPost(data);
    };
    setLoading(false);
    fetchPost();
  }, []);

  return (
    <div>
      {loading && <p>Loading...</p>}
      {!loading && (
        <>
          {post.id && (
            <Post
              description={post.description}
              url={post.image && post.image.url}
              likes={post.likes}
            />
          )}
          {!post.id && <p styles="color: red">404 - not found</p>}
        </>
      )}
    </div>
  );
};

export default SinglePost;
