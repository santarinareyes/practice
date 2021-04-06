import { NavLink } from "react-router-dom";

const Nav = () => (
  <div className="Nav">
    <NavLink className="main__nav" to="/" exact>
      Home
    </NavLink>
    <NavLink className="main__nav" to="/create" exact>
      Create
    </NavLink>
  </div>
);

export default Nav;
