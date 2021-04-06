import { useState } from "react";

const Create = () => {
  const [description, setDescription] = useState("");
  const [file, setFile] = useState(null);

  const handleSubmit = async (event) => {
    // event.preventDefault();

    const formData = new FormData();
    formData.append("data", JSON.stringify({ description }));
    formData.append("files.image", file);

    const response = await fetch("http://localhost:1337/posts", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();
    console.log(data);
  };

  return (
    <div className="Create">
      <h2>Create</h2>
      <form className="Create__Form" onSubmit={handleSubmit}>
        <input
          type="text"
          name="description"
          value={description}
          onChange={(event) => setDescription(event.target.value)}
          placeholder="Description..."
        />
        <input
          type="file"
          name="file"
          onChange={(event) => setFile(event.target.files[0])}
          placeholder="Add a file..."
        />
        <button>Submit</button>
      </form>
    </div>
  );
};

export default Create;
