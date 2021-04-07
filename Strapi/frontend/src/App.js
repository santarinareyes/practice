import { BrowserRouter, Switch, Route } from "react-router-dom";
import Nav from "./components/Nav";
import Home from "./pages/Home";
import Create from "./pages/Create";
import SinglePost from "./pages/SinglePost";
import "./App.css";

function App() {
  return (
    <div className="App">
      <BrowserRouter>
        <Nav />
        <Switch>
          <Route path="/" exact component={Home} />
          <Route path="/create" exact component={Create} />
          <Route path="/:id" exact component={SinglePost} />
        </Switch>
      </BrowserRouter>
    </div>
  );
}

export default App;
