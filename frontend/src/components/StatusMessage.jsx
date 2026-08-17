export function ErrorMessage({ error }) {
  if (!error) return null;

  const message =
    error?.response?.data?.message ||
    (error?.response?.data?.errors &&
      Object.values(error.response.data.errors).flat().join(" ")) ||
    error?.message ||
    "Došlo je do greške.";

  return <p className="error-message">{message}</p>;
}

export function LoadingSpinner() {
  return <p className="loading">Učitavanje…</p>;
}
