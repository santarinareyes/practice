import { useState } from "react";

const Create = () => {
  const [description, setDescription] = useState("");
  const [file, setFile] = useState(null);
  const [error, setError] = useState("");

  const handleSubmit = async (event) => {
    event.preventDefault();

    if (!description || !file) {
      let errMessage = "";
      errMessage += !description ? "Please add a description" : "";

      if (!file) {
        errMessage += !description ? " and a file" : "Please add a file";
      }

      return setError(errMessage);
    }

    const formData = new FormData();
    formData.append("data", JSON.stringify({ description }));
    formData.append("files.image", file);

    try {
      const response = await fetch("http://localhost:1337/posts", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();
      console.log(data);
    } catch (err) {
      console.log(err);
      setError(err);
    }
  };

  return (
    <div className="Create">
      <h2>Create</h2>
      {error && <p>{error}</p>}
      <form className="Create__Form" onSubmit={handleSubmit}>
        <input
          type="text"
          name="description"
          value={description}
          onChange={(event) => {
            setError("");
            setDescription(event.target.value);
          }}
          placeholder="Description..."
        />
        <input
          type="file"
          name="file"
          onChange={(event) => {
            setError("");
            setFile(event.target.files[0]);
          }}
          placeholder="Add a file..."
        />
        <button>Submit</button>
      </form>
    </div>
  );
};

export default Create;
