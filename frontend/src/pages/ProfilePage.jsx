import { useRef, useState } from "react";
import { useAuth } from "../context/AuthContext";
import * as authApi from "../api/auth";
import { ErrorMessage } from "../components/StatusMessage";

export default function ProfilePage() {
  const { user, refreshUser } = useAuth();
  const fileInput = useRef(null);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(false);

  async function handleUpload(e) {
    e.preventDefault();
    const file = fileInput.current?.files?.[0];
    if (!file) return;
    setUploading(true);
    setError(null);
    setSuccess(false);
    try {
      await authApi.uploadAvatar(file);
      await refreshUser();
      setSuccess(true);
    } catch (err) {
      setError(err);
    } finally {
      setUploading(false);
    }
  }

  if (!user) return null;

  return (
    <div className="page">
      <h1>Profil</h1>
      <div className="profile-card">
        {user.avatar_url && (
          <img src={user.avatar_url} alt="" className="avatar-large" />
        )}
        <dl>
          <dt>Ime</dt>
          <dd>{user.name}</dd>
          <dt>Email</dt>
          <dd>{user.email}</dd>
          <dt>Uloga</dt>
          <dd>{user.role}</dd>
        </dl>
      </div>

      <h2>Profilna slika</h2>
      <form onSubmit={handleUpload} className="form form-inline">
        <input type="file" accept="image/*" ref={fileInput} />
        <button type="submit" disabled={uploading}>
          {uploading ? "Otpremanje…" : "Otpremi"}
        </button>
      </form>
      {success && <p className="notice">Profilna slika je ažurirana.</p>}
      <ErrorMessage error={error} />
    </div>
  );
}
