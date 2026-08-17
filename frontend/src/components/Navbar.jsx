import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function Navbar() {
  const { isAuthenticated, isAdmin, user, logout } = useAuth();
  const navigate = useNavigate();

  async function handleLogout() {
    await logout();
    navigate("/");
  }

  return (
    <header className="navbar">
      <div className="navbar-inner">
        <Link to="/" className="brand">
          VebForum
        </Link>
        <nav className="nav-links">
          <Link to="/topics">Teme</Link>
          <Link to="/posts">Postovi</Link>
          <Link to="/statistics">Statistika</Link>
          {isAdmin && <Link to="/admin/users">Korisnici</Link>}
        </nav>
        <div className="nav-auth">
          {isAuthenticated ? (
            <>
              <Link to="/profile" className="nav-user">
                {user?.avatar_url && (
                  <img
                    src={user.avatar_url}
                    alt=""
                    className="avatar-thumb"
                  />
                )}
                {user?.name ?? "Profil"}
              </Link>
              <button type="button" onClick={handleLogout}>
                Odjava
              </button>
            </>
          ) : (
            <>
              <Link to="/login">Prijava</Link>
              <Link to="/register">Registracija</Link>
            </>
          )}
        </div>
      </div>
    </header>
  );
}
