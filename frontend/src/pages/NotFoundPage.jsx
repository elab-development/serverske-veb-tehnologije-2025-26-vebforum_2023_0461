import { Link } from "react-router-dom";

export default function NotFoundPage() {
  return (
    <div className="page">
      <h1>404 – Stranica nije pronađena</h1>
      <p>
        <Link to="/">Nazad na početnu</Link>
      </p>
    </div>
  );
}
