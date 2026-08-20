import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import * as authApi from "../api/auth";
import { ErrorMessage } from "../components/StatusMessage";

export default function ResetPasswordPage() {
  const navigate = useNavigate();
  const [form, setForm] = useState({
    email: "",
    token: "",
    password: "",
    password_confirmation: "",
  });
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  async function handleSubmit(e) {
    e.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      await authApi.resetPassword(form);
      setSuccess(true);
      setTimeout(() => navigate("/login"), 1500);
    } catch (err) {
      setError(err);
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div className="auth-page">
      <h1>Resetovanje lozinke</h1>
      {success ? (
        <p className="notice">
          Lozinka je uspešno promenjena. Preusmeravanje na prijavu…
        </p>
      ) : (
        <form onSubmit={handleSubmit} className="form">
          <label>
            Email
            <input
              type="email"
              required
              value={form.email}
              onChange={(e) => setForm({ ...form, email: e.target.value })}
            />
          </label>
          <label>
            Token
            <input
              required
              value={form.token}
              onChange={(e) => setForm({ ...form, token: e.target.value })}
            />
          </label>
          <label>
            Nova lozinka
            <input
              type="password"
              required
              minLength={6}
              value={form.password}
              onChange={(e) =>
                setForm({ ...form, password: e.target.value })
              }
            />
          </label>
          <label>
            Potvrda lozinke
            <input
              type="password"
              required
              minLength={6}
              value={form.password_confirmation}
              onChange={(e) =>
                setForm({ ...form, password_confirmation: e.target.value })
              }
            />
          </label>
          <ErrorMessage error={error} />
          <button type="submit" disabled={submitting}>
            {submitting ? "Menjanje…" : "Promeni lozinku"}
          </button>
        </form>
      )}
      <p>
        <Link to="/login">Nazad na prijavu</Link>
      </p>
    </div>
  );
}
