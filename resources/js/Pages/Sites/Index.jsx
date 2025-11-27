// resources/js/Pages/Sites/Index.jsx

import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

function StatusBadge({ status }) {
    const normalized = (status || '').toLowerCase();

    let colorClasses =
        'bg-gray-100 text-gray-700 border border-gray-300';

    if (normalized === 'running') {
        colorClasses = 'bg-green-100 text-green-800 border border-green-300';
    } else if (normalized === 'stopped') {
        colorClasses = 'bg-yellow-100 text-yellow-800 border border-yellow-300';
    } else if (normalized === 'failed') {
        colorClasses = 'bg-red-100 text-red-800 border border-red-300';
    } else if (normalized === 'deploying') {
        colorClasses = 'bg-blue-100 text-blue-800 border border-blue-300';
    }

    return (
        <span
            className={
                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide ' +
                colorClasses
            }
        >
            {status ? status.toUpperCase() : 'UNKNOWN'}
        </span>
    );
}

export default function Index({ auth, sites }) {
    const handleDelete = (site) => {
        if (!confirm(`Delete "${site.name}"? This will also remove its Docker stack from the VPS.`)) {
            return;
        }

        router.delete(route('sites.destroy', site.id));
    };

    const handleStart = (site) => {
        router.post(route('sites.start', site.id));
    };

    const handleStop = (site) => {
        router.post(route('sites.stop', site.id));
    };

    const handleRedeploy = (site) => {
        router.post(route('sites.redeploy', site.id));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    WordPress Sites
                </h2>
            }
        >
            <Head title="WordPress Sites" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="px-6 py-4 border-b flex items-center justify-between">
                            <div>
                                <h3 className="text-lg font-medium text-gray-900">
                                    Managed Sites
                                </h3>
                                <p className="mt-1 text-sm text-gray-500">
                                    Simple dashboard to manage your Docker-based WordPress installs.
                                </p>
                            </div>

                            <Link
                                href={route('sites.create')}
                                className="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                + New Site
                            </Link>
                        </div>

                        <div className="p-6 overflow-x-auto">
                            {sites.length === 0 ? (
                                <p className="text-sm text-gray-500">
                                    No sites yet. Click &ldquo;New Site&rdquo; to create your first
                                    WordPress instance.
                                </p>
                            ) : (
                                <table className="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                                                Domain
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                                                Container
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                                                HTTP Port
                                            </th>
                                            <th className="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th className="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {sites.map((site) => (
                                            <tr key={site.id}>
                                                <td className="px-4 py-3 whitespace-nowrap text-gray-900">
                                                    {site.name}
                                                </td>
                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    <a
                                                        href={
                                                            site.domain.startsWith('http')
                                                                ? site.domain
                                                                : `http://${site.domain}`
                                                        }
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="text-indigo-600 hover:text-indigo-800"
                                                    >
                                                        {site.domain}
                                                    </a>
                                                </td>
                                                <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                    {site.container_name}
                                                </td>
                                                <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                                    {site.http_port}
                                                </td>
                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    <StatusBadge status={site.status} />
                                                </td>
                                                <td className="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                    <div className="inline-flex items-center space-x-3">
                                                        <Link
                                                            href={route('sites.edit', site.id)}
                                                            className="text-indigo-600 hover:text-indigo-900"
                                                        >
                                                            Edit
                                                        </Link>
                                                        {site.status === 'running' ? (
                                                            <button
                                                                type="button"
                                                                onClick={() => handleStop(site)}
                                                                className="text-yellow-600 hover:text-yellow-800"
                                                            >
                                                                Stop
                                                            </button>
                                                        ) : (
                                                            <button
                                                                type="button"
                                                                onClick={() => handleStart(site)}
                                                                className="text-green-600 hover:text-green-800"
                                                            >
                                                                Start
                                                            </button>
                                                        )}
                                                        <button
                                                            type="button"
                                                            onClick={() => handleRedeploy(site)}
                                                            className="text-blue-600 hover:text-blue-800"
                                                        >
                                                            Redeploy
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() => handleDelete(site)}
                                                            className="text-red-600 hover:text-red-800"
                                                        >
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
