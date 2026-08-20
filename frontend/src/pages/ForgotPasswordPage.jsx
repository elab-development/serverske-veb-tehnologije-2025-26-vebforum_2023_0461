import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import * as authApi from "../api/auth";
import { ErrorMessage } from "../components/StatusMessage";

export default function ForgotPasswordPage() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [error, setError] = useState(null);
  const [submitting, setSubmitting] = useState(false);
  const [resetToken, setResetToken] = useState(null);

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      const data = await authApi.forgotPassword(email);
      setResetToken(data.reset_token);
    } catch (err) {
      setError(err);
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div className="auth-page">
      <h1>Zaboravljena lozinka</h1>
      {resetToken ? (
        <div className="notice">
          <p>
            Token za resetovanje lozinke je generisan. U produkcionom
            okruženju bio bi poslat na email; ovde je prikazan direktno radi
            testiranja:
          </p>
          <code className="token-box">{resetToken}</code>
          <button type="button" onClick={() => navigate("/reset-password")}>
            Nastavi na resetovanje lozinke
          </button>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="form">
          <label>
            Email
            <input
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />
          </label>
          <ErrorMessage error={error} />
          <button type="submit" disabled={submitting}>
            {submitting ? "Slanje…" : "Pošalji zahtev"}
          </button>
        </form>
      )}
      <p>
        <Link to="/login">Nazad na prijavu</Link>
      </p>
    </div>
  );
}
